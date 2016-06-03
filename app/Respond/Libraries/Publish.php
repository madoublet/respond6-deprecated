<?php

namespace App\Respond\Libraries;

// respond libraries
use App\Respond\Models\Site;
use App\Respond\Models\User;
use App\Respond\Models\Page;
use App\Respond\Models\Form;
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
     * Publishes blocks
     *
     * @param {Site} $site
     */
    public static function publishBlocks($user, $site)
    {
        // get blocks
        $dir = app()->basePath().'/public/sites/'.$site->id.'/blocks/';
        $exts = array('html');

        $files = Utilities::listFiles($dir, $site->id, $exts);
        $blocks = array();

        foreach($files as $file) {

          $path = app()->basePath().'/public/sites/'.$site->id.'/'.$file;

          if(file_exists($path)) {

            $html = file_get_contents($path);
            $id = basename($path);
            $id = str_replace('.html', '', $id);

            // push block to array
            array_push($blocks, $id);

          }

        }

        // setup twig
        $loader = new \Twig_Loader_Filesystem(app()->basePath().'/public/sites/'.$site->id.'/blocks');

        $twig = new \Twig_Environment($loader);

        // get all pages
        $pages = Page::listAll($user, $site);

        // list all forms
        $forms = Form::listExtended($site->id);

        foreach($pages as $item) {

          // get page
          $page = new Page($item);

          $location = app()->basePath().'/public/sites/'.$site->id.'/'.$page->url.'.html';

          // get html from page
          $html = file_get_contents($location);

          // walk through blocks
          foreach($blocks as $block) {

            // insert into block comments
            $start = '<!-- block:'.$block.' -->';
            $end = '<!-- /block:'.$block.' -->';

            // check for start and end
            if(strpos($html, $start) !== FALSE && strpos($html, $end) !== FALSE) {

              // load the template
              $template = $twig->loadTemplate($block.'.html');

              // render the template
              $block_html = $template->render(array('pages' => $pages));

              // replace content
              $html = Utilities::replaceBetween($html, $start, $end, $block_html);
            }

          }

          // load the parser
          $dom = HtmlDomParser::str_get_html($html, $lowercase=true, $forceTagsClosed=false, $target_charset=DEFAULT_TARGET_CHARSET, $stripRN=false, $defaultBRText=DEFAULT_BR_TEXT, $defaultSpanText=DEFAULT_SPAN_TEXT);

          // insert into [block] elements
          foreach($dom->find('[block]') as $el) {

            if(isset($el->type)) {

              if(array_search($el->type, $blocks) !== FALSE) {

                // load the template
                $template = $twig->loadTemplate($el->type.'.html');

                $render_arr = array('pages' => $pages, 'forms' => $forms, 'attributes' => $el->attr);

                // render the template
                $block_html = $template->render($render_arr);

                // set the inner text
                $el->innertext = $block_html;

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