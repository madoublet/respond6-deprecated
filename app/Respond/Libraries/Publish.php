<?php

namespace App\Respond\Libraries;

use App\Respond\Models\Site;
use App\Respond\Models\User;
use App\Respond\Models\Page;
use App\Respond\Libraries\Utilities;
use App\Respond\Libraries\Components;

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
     * Pubishes components
     *
     * @param {Site} $site
     */
    public static function publishComponents($site)
    {

        // production
        $dir = app()->basePath() . '/node_modules';

        if (env('APP_ENV') == 'development') {
            $dir = app()->basePath() . '/public/dev';
        }

        // publish components polyfil
        $src = $dir . '/respond-components/bower_components/webcomponentsjs';
        $dest = app()->basePath() . '/public/sites/' . $site->id . '/components/lib';

        // copy the directory
        Utilities::copyDirectory($src, $dest);

        // paths to build file
        $src = $dir . '/respond-components/build';
        $dest = app()->basePath() . '/public/sites/' . $site->id . '/components/';

        // copy the directory
        Utilities::copyDirectory($src, $dest);

    }

    /**
     * Pubishes snippets
     *
     * @param {Site} $site
     */
    public static function publishSnippets($user, $site)
    {
        // get snippets
        $dir = app()->basePath().'/public/sites/'.$site->id.'/snippets/';
        $exts = array('html');

        $files = Utilities::listFiles($dir, $site->id, $exts);
        $snippets = array();

        foreach($files as $file) {

          $path = app()->basePath().'/public/sites/'.$site->id.'/'.$file;

          if(file_exists($path)) {

            $html = file_get_contents($path);
            $id = basename($path);
            $id = str_replace('.html', '', $id);

            // push snippet to array
            array_push($snippets, array(
              'id' => $id,
              'html' => $html
              ));

          }

        }

        // get all pages
        $arr = Page::listAll($user, $site);

        foreach($arr as $item) {

          // get page
          $page = new Page($item);

          $location = app()->basePath().'/public/sites/'.$site->id.'/'.$page->url.'.html';

          // get layout html
          $html = file_get_contents($location);

          foreach($snippets as $snippet) {

            $start = '<!-- snippet:'.$snippet['id'].' -->';
            $end = '<!-- /snippet:'.$snippet['id'].' -->';

            // check for start and end
            if(strpos($html, $start) !== FALSE && strpos($html, $end) !== FALSE) {
              $html = Utilities::replaceBetween($html, $start, $end, $snippet['html']);
            }

          }

          // put html back
          file_put_contents($location, $html);

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

    /**
     * Republish site components
     *
     * @param {Site} $site
     * @param {Page} $page
     * @param {User} $user
     */
    public static function republishComponents($site, $user) {

      $arr = Page::listAll($user, $site);

      foreach($arr as $item) {

        // get page
        $page = new Page($item);

        $location = app()->basePath().'/public/sites/'.$site->id.'/'.$page->url.'.html';

        // get layout html
        $html = file_get_contents($location);

        // get phpQuery of file
        $doc = \phpQuery::newDocument($html);

        foreach ($doc['[respond-menu], respond-menu'] as $el) {

            $type = pq($el)->attr('type');
            $cssClass = pq($el)->attr('class');

            // get the type
            if ($type != NULL) {

              $attrs = array('type' => $type, 'cssClass' => $cssClass);

              // get the menu HTML
              $html = Components::respondMenu($attrs, $site, $page);

              // get menu HTML
              pq($el)->replaceWith($html);

            }
            /* isset */

        }
        /* foreach */

        // publish
        $html = $doc->htmlOuter();
        file_put_contents($location, $html);

      }

    }

}