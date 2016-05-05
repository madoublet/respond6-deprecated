<?php

namespace App\Respond\Models;

use App\Respond\Libraries\Utilities;
use App\Respond\Libraries\Publish;

use App\Respond\Models\Site;
use App\Respond\Models\User;

/**
 * Models setting
 */
class Setting {

  public $id;
  public $label;
  public $description;
  public $type;
  public $value;

  /**
   * Constructs a page from an array of data
   *
   * @param {arr} $data
   */
  function __construct(array $data) {
    foreach($data as $key => $val) {
      if(property_exists(__CLASS__,$key)) {
        $this->$key = $val;
      }
    }
  }

  /**
   * lists all settings
   *
   * @param {files} $data
   * @return {array}
   */
  public static function listAll($siteId) {

    $file = app()->basePath().'/resources/sites/'.$siteId.'/settings.json';

    return json_decode(file_get_contents($file), true);

  }

  /**
   * Saves all settings
   *
   * @param {string} $name
   * @param {string} $siteId site id
   * @return Response
   */
  public static function saveAll($settings, $user, $site) {

    // get file
    $file = app()->basePath().'/resources/sites/'.$site->id.'/settings.json';

    // get settings
    if(file_exists($file)) {

      file_put_contents($file, json_encode($settings, JSON_PRETTY_PRINT));

      // update settings in the pages
      $arr = Page::listAll($user, $site);

      foreach($arr as $item) {

        // get page
        $page = new Page($item);

        $path = app()->basePath().'/public/sites/'.$site->id.'/'.$page->url.'.html';

        // get contents of the page
        $html = file_get_contents($path);

        // open document
        $doc = \phpQuery::newDocument($html);

        // walk through settings
        foreach($settings as $setting) {

          // handle sets
          if(isset($setting['sets'])) {

            // set attribute
            if(isset($setting['attribute'])) {


              $doc['['.$setting['id'].']']->attr($setting['attribute'], $setting['value']);

            }

          }

        }

        file_put_contents($path, $doc->htmlOuter());

      }

      return TRUE;


    }

    return FALSE;

  }

}