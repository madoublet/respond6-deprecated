<?php

namespace App\Http\Controllers;

use \Illuminate\Http\Request;

use App\Respond\Libraries\Utilities;

use App\Respond\Models\Form;
use App\Respond\Models\Gallery;
use App\Respond\Models\Setting;

class EditController extends Controller
{

    /**
     * Edits a page provided by the querystring, in format ?q=site-name/dir/page.html
     *
     * @return Response
     */
    public function edit(Request $request)
    {

        $q = $request->input('q');

        if($q != NULL){

          $arr = explode('/', $q);

          if(sizeof($arr) > 0) {

            $siteId = $arr[0];
            
            // set html if hiddne
            $url = $q;
            
            // strip any trailing .html from url
            $url = preg_replace('/\\.[^.\\s]{3,4}$/', '', $url);
            
            // add .html for non-friendly URLs
            if(env('FRIENDLY_URLS') === false) {
              $url .= '.html';
            }
            
            // load page
            $path = rtrim(app()->basePath('public/sites/'.$url), '/');

            if(file_exists($path)) {

              $html = file_get_contents($path);

              // open document
              $doc = \phpQuery::newDocument($html);

              // set base
              $doc['base']->attr('href', '/sites/'.$siteId.'/');

              // get settings
              $sortable = Setting::getById('sortable', $siteId);
              $editable = Setting::getById('editable', $siteId);

              // defaults
              if($sortable === NULL) {
                $sortable = '.col, .column';
              }

              if($editable === NULL) {
                $editable = ['[role="main"]'];
              }
              else {
                $editable = explode(',', $editable);
                // trim elements in the array
                $editable = array_map('trim', $editable);
              }
    
              // set active attribute
              $doc['body']->attr('hashedit-active', '');
              
              // setup editable area
              foreach($editable as $value){
                $doc[$value]->attr('hashedit', '');
                $doc[$value]->attr('hashedit-selector', $value);
              }


              // init
              $plugins_script = '';

              // get custom plugins
              $js_file = app()->basePath().'/resources/sites/'.$siteId.'/plugins.js';

              if(file_exists($js_file)) {

                if(file_exists($js_file)) {
                  $plugins_script .= file_get_contents($js_file);
                }

              }
              
              // inject forms into script
              if(strpos($plugins_script, 'respond.forms') !== false ) {

                $arr = Form::listAll($siteId);
                $options = array();

                // get id
                foreach($arr as $item) {
                  array_push($options, array(
                    'text' => $item['name'],
                    'value' => $item['id']
                  ));
                }

                // inject forms into script
                $plugins_script = str_replace("['respond.forms']", json_encode($options), $plugins_script);
              }

              // inject galleries into script
              if(strpos($plugins_script, 'respond.galleries') !== false ) {

                $arr = Gallery::listAll($siteId);
                $options = array();

                // get id
                foreach($arr as $item) {
                  array_push($options, array(
                    'text' => $item['name'],
                    'value' => $item['id']
                  ));
                }

                // inject galleries into script
                $plugins_script = str_replace("['respond.galleries']", json_encode($options), $plugins_script);
              }

              // inject routes into script
              if(strpos($plugins_script, 'respond.routes') !== false ) {

                $dir = $file = app()->basePath().'/public/sites/'.$siteId;
                $arr = array_merge(array('/'), Utilities::listRoutes($dir, $siteId));

                $options = array();

                // get id
                foreach($arr as $item) {
                  array_push($options, array(
                    'text' => $item,
                    'value' => $item
                  ));
                }

                // inject galleries into script
                $plugins_script = str_replace("['respond.routes']", json_encode($options), $plugins_script);
              }
              
              // inject pages into script
              if(strpos($plugins_script, 'respond.pages') !== false ) {

                $arr = Pages::listAllBySite($siteId);
                $options = array();

                // get id
                foreach($arr as $item) {
                  array_push($options, array(
                    'text' => $item['title'],
                    'value' => $item['url']
                  ));
                }

                // inject galleries into script
                $plugins_script = str_replace("['respond.galleries']", json_encode($options), $plugins_script);
              }

              // setup references
              $els = $doc['[hashedit-exclude]'];

              // add references to each element
              foreach($els as $el) {
                pq($el)->remove();
              }

              // setup references
              $els = $doc['body *'];
              $i = 1;

              // add references to each element
              foreach($els as $el) {
                pq($el)->attr('data-ref', $i);
                $i++;
              }

              if(env('APP_ENV') == 'development') {

                // hashedit development stack
                $hashedit = <<<EOD
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">
<link type="text/css" href="/node_modules/dropzone/dist/min/dropzone.min.css" rel="stylesheet">
<script src="/dev/hashedit/js/fetch.min.js"></script>
<script src="/dev/hashedit/js/i18next.js"></script>
<script src="/node_modules/dropzone/dist/min/dropzone.min.js"></script>
<script src="/node_modules/sortablejs/Sortable.min.js"></script>
<script src="/dev/hashedit/js/hashedit.js"></script>
<script>$plugins_script</script>
<script>
hashedit.setup({
  dev: true,
  url: '$url',
  sortable: '$sortable',
  login: '/login/$siteId',
  translate: true,
  languagePath: '/i18n/{{language}}.json',
  auth: 'token',
  authHeader: 'X-AUTH'
});
</script>
EOD;
}
              else {
              
                // hashedit production stack
                $hashedit = <<<EOD
<link href="https://fonts.googleapis.com/css?family=Open+Sans:400,700" rel="stylesheet" type="text/css">
<link type="text/css" href="/node_modules/dropzone/dist/min/dropzone.min.css" rel="stylesheet">
<script src="/node_modules/hashedit/js/fetch.min.js"></script>
<script src="/node_modules/hashedit/js/i18next.js"></script>
<script src="/node_modules/dropzone/dist/min/dropzone.min.js"></script>
<script src="/node_modules/sortablejs/Sortable.min.js"></script>
<script src="/node_modules/hashedit/js/hashedit.js"></script>
<script>$plugins_script</script>
<script>
hashedit.setup({
  url: '$url'
  sortable: '$sortable',
  login: '/login/$siteId'
  translate: true,
  languagePath: '/i18n/{{language}}.json',
  auth: 'token',
  authHeader: 'X-AUTH'
});
</script>
EOD;
}

              $hashedit .= '<style type="text/css">'.
                            '.respond-plugin {'.
                            '  position: relative;'.
                            '  padding: 10px 0;'.
                            '  margin: 0 0 20px 0;'.
                            '  background-color: #f8f8f8;'.
                            '  border: 1px solid #f0f0f0;'.
                            '  text-align: center;'.
                            '  color: #aaa;'.
                            '}'.
                            '.respond-plugin span {'.
                            '  display: block;'.
                            '  margin: 0; padding: 0;'.
                            '  color: #aaa;'.
                            '  text-align: center;'.
                            '  text-transform: uppercase;'.
                            '  font-size: 11px;'.
                            '  font-family: "Open Sans", sans-serif;'.
                            '}'.
                            '.respond-plugin svg{'.
                            '  fill: currentColor;'.
                            '  width: 35px;'.
                            '  height: 35px;'.
                            '}';

              // load the edit library
              $doc['body']->append($hashedit);

              // get updated html
              $contents = $doc->htmlOuter();

              return $contents;

            }


          }


        }

    }

    /**
     * Retrieves a mirror for the html
     *
     * @return Response
     */
    public function mirror(Request $request)
    {

        $q = $request->input('q');

        if($q != NULL){

          $arr = explode('/', $q);

          if(sizeof($arr) > 0) {

            $siteId = $arr[0];

            // load page
            $path = rtrim(app()->basePath('public/sites/'.$q), '/');

            if(file_exists($path)) {

              $html = file_get_contents($path);

              // open document
              $doc = \phpQuery::newDocument($html);

              // setup references
              $els = $doc['body *'];
              $i = 1;

              // add references to each element
              foreach($els as $el) {
                pq($el)->attr('data-ref', $i);
                $i++;
              }

              // setup editable area
              $editable = ['[role="main"]'];

              foreach($editable as $value){
                $doc[$value]->attr('hashedit', '');
                $doc[$value]->attr('hashedit-selector', $value);
              }

              // remove any scripts that could mess up the DOM
              $els = $doc['script'];

              foreach($els as $el) {
                pq($el)->remove();
              }

              // remove any links that could mess up the dom
              $els = $doc['link'];

              foreach($els as $el) {
                pq($el)->remove();
              }

              // get updated html
              $contents = $doc->htmlOuter();

              return $contents;

            }


          }


        }

    }


}