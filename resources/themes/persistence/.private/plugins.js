/**
 * Custom plugins
 */
var custom = (function() {

  'use strict';

  return {

    // set version
    version: '1.0.0',

    // setup plugins
    plugins: []

  }

})();

// add plugins
if(hashedit.menu !== null && hashedit.menu !== undefined) {
  hashedit.menu = hashedit.menu.concat(custom.plugins); 
}