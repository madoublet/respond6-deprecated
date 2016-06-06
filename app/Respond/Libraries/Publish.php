<?php

namespace App\Respond\Libraries;

// respond libraries
use App\Respond\Models\Site;
use App\Respond\Models\User;
use App\Respond\Models\Page;
use App\Respond\Models\Form;
use App\Respond\Models\Menu;
use App\Respond\Models\Gallery;
use App\Respond\Libraries\Utilities;

// DOM parser
use Sunra\PhpSimple\HtmlDomParser;

class Publish
{

    /**
     * Pubishes the theme to the site
     *
     * @param {Site} $site
     */
    public static function publishTheme($theme, $site)
    {

        // publish theme files
        $src = app()->basePath() . '/resources/themes/' . $theme;
        $dest = app()->basePath() . '/public/sites/' . $site->id;

        // copy the directory
        Utilities::copyDirectory($src, $dest);

        // copy the private files
        $src = app()->basePath() . '/resources/themes/' . $theme . '/.private';
        $dest = app()->basePath() . '/resources/sites/' . $site->id;

        // copy the directory
        Utilities::copyDirectory($src, $dest);

    }

    /**
     * Pubishes the localse to the site
     *
     * @param {Site} $site
     */
    public static function publishLocales($site)
    {

        // publish theme files
        $src = app()->basePath() . '/resources/locales';
        $dest = app()->basePath() . '/public/sites/' . $site->id . '/locales';

        // copy the directory
        Utilities::copyDirectory($src, $dest);
    }


    /**
     * Publishes plugins for the site
     *
     * @param {Site} $site
     */
    public static function publishPlugins($user, $site)
    {
        // get plugins for the site
        $dir = app()->basePath().'/public/sites/'.$site->id.'/plugins/';
        $exts = array('html');

        $files = Utilities::listFiles($dir, $site->id, $exts);
        $plugins = array();

        foreach($files as $file) {

          $path = app()->basePath().'/public/sites/'.$site->id.'/'.$file;

          if(file_exists($path)) {

            $html = file_get_contents($path);
            $id = basename($path);
            $id = str_replace('.html', '', $id);

            // push plugin to array
            array_push($plugins, $id);

          }

        }

        // setup twig
        $loader = new \Twig_Loader_Filesystem(app()->basePath().'/public/sites/'.$site->id.'/plugins');

        $twig = new \Twig_Environment($loader);

        // get all pages
        $pages = Page::listAll($user, $site);

        // list all forms, menus, galleries
        $forms = Form::listExtended($site->id);
        $menus = Menu::listExtended($site->id);
        $galleries = Gallery::listExtended($site->id);

        foreach($pages as $item) {

          // get page
          $page = new Page($item);

          // setup current page
          $current_page = array(
            'url' => $page->url,
            'title' => $page->title,
            'description' => $page->description,
            'keywords' => $page->keywords,
            'callout' => $page->callout,
            'photo' => $page->photo,
            'thumb' => $page->thumb,
            'language' => $page->language,
            'direction' => $page->direction,
            'firstName' => $page->firstName,
            'lastName' => $page->lastName,
            'lastModifiedBy' => $page->lastModifiedBy,
            'lastModifiedDate' => $page->lastModifiedDate
          );

          $location = app()->basePath().'/public/sites/'.$site->id.'/'.$page->url.'.html';

          // get html from page
          $html = file_get_contents($location);

          // walk through plugins
          foreach($plugins as $plugin) {

            // insert into respond-plugin comments
            $start = '<!-- respond-plugin:'.$plugin.' -->';
            $end = '<!-- /respond-plugin:'.$plugin.' -->';

            // check for start and end
            if(strpos($html, $start) !== FALSE && strpos($html, $end) !== FALSE) {

              // load the template
              $template = $twig->loadTemplate($plugin.'.html');

              // render the template
              $plugin_html = $template->render(array('pages' => $pages));

              // replace content
              $html = Utilities::replaceBetween($html, $start, $end, $plugin_html);
            }

          }

          // load the parser
          $dom = HtmlDomParser::str_get_html($html, $lowercase=true, $forceTagsClosed=false, $target_charset=DEFAULT_TARGET_CHARSET, $stripRN=false, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT);

          // insert into [respond-plugin] elements
          foreach($dom->find('[respond-plugin]') as $el) {

            if(isset($el->type)) {

              if(array_search($el->type, $plugins) !== FALSE) {

                // load the template
                $template = $twig->loadTemplate($el->type.'.html');

                $render_arr = array('page' => $current_page, 'pages' => $pages, 'forms' => $forms, 'galleries' => $galleries, 'menus' => $menus, 'attributes' => $el->attr);

                // render the template
                $plugin_html = $template->render($render_arr);

                // set the inner text
                $el->innertext = $plugin_html;

              }

            }

          }

          // put html back
          file_put_contents($location, $dom);

        }

    }

    /**
     * Injects site settings the JS to the site
     *
     * @param {Site} $site
     */
    public static function injectSiteSettings($site)
    {
        // inject whether to use friendly urls
        $useFriendlyURLs = false;

        if(env('FRIENDLY_URLS') === true || env('FRIENDLY_URLS') === 'true') {
          $useFriendlyURLs = true;
        }

        // create settings
        $settings = array(
            'id' => $site->id,
            'api' => Utilities::retrieveAppUrl() . '/api',
            'useFriendlyURLs' => $useFriendlyURLs
        );

        // settings
        $str_settings = json_encode($settings);

        // get site file
        $file = app()->basePath() . '/public/sites/' . $site->id . '/js/respond.site.js';

        if (file_exists($file)) {

            // get contents
            $content = file_get_contents($file);

            $start = 'settings: {';
            $end = '}';

            // remove { }
            $new = str_replace('{', '', $str_settings);
            $new = str_replace('}', '', $new);

            // replace
            $content = preg_replace('#(' . preg_quote($start) . ')(.*?)(' . preg_quote($end) . ')#si', '$1' . $new . '$3', $content);

            // publish updates
            file_put_contents($file, $content);

        }

    }

}