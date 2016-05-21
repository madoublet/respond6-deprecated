/**
 * Core respond plugins
 */
var respond = respond || {};

respond.core = (function() {

  'use strict';

  return {

    // set version
    version: '1.0.0',

    // setup plugins
    plugins: [{
      action: "respond.map",
      selector: "respond-map",
      title: "Map",
      display: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="100%" width="100%"><path d="M20.5 3l-.16.03L15 5.1 9 3 3.36 4.9c-.21.07-.36.25-.36.48V20.5c0 .28.22.5.5.5l.16-.03L9 18.9l6 2.1 5.64-1.9c.21-.07.36-.25.36-.48V3.5c0-.28-.22-.5-.5-.5zM15 19l-6-2.11V5l6 2.11V19z"/><path d="M0 0h24v24H0z" fill="none"/></svg>',
      view: '<div class="respond-plugin"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="100%" width="100%"><path d="M20.5 3l-.16.03L15 5.1 9 3 3.36 4.9c-.21.07-.36.25-.36.48V20.5c0 .28.22.5.5.5l.16-.03L9 18.9l6 2.1 5.64-1.9c.21-.07.36-.25.36-.48V3.5c0-.28-.22-.5-.5-.5zM15 19l-6-2.11V5l6 2.11V19z"/><path d="M0 0h24v24H0z" fill="none"/></svg></div>',
      html: '<respond-map address="100 Washington Ave, St Louis, MO 63102" zoom="12"></respond-map>',
      attributes: [
        {
          attr: 'address',
          label: 'Address',
          type: 'text'
        },
        {
          attr: 'zoom',
          label: 'Zoom',
          type: 'select',
          values: ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20']
        }
      ]
    },
    {
      action: "respond.form",
      selector: "respond-form",
      title: "Map",
      display: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="100%" width="100%"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
      view: '<div class="respond-plugin"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="100%" width="100%"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg></div>',
      html: '<respond-form id="contact-us"></respond-form>',
      attributes: [
        {
          attr: 'id',
          label: 'Form',
          type: 'select',
          values: ['respond.forms']
        }
      ]
    },
    {
      action: "respond.list",
      selector: "respond-list",
      title: "List",
      display: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="100%" width="100%"><path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z"/><path d="M0 0h24v24H0z" fill="none"/></svg>',
      view: '<div class="respond-plugin"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="100%" width="100%"><path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z"/><path d="M0 0h24v24H0z" fill="none"/></svg></div>',
      html: '<respond-list display="list" url="page/" orderby="name" pageresults="false" pagesize="10"></respond-list>',
      attributes: [
        {
          attr: 'url',
          label: 'Url',
          type: 'select',
          values: ['respond.routes']
        },
        {
          attr: 'display',
          label: 'Display',
          type: 'select',
          values: [
            {
              value: 'list',
              text: 'List'
            },
            {
              value: 'list-blog',
              text: 'Blog'
            },
            {
              value: 'list-thumbnails',
              text: 'Thumbnails'
            }
          ]
        },
        {
          attr: 'orderby',
          label: 'Order By',
          type: 'select',
          values: [
            {
              value: 'name',
              text: 'Name'
            },
            {
              value: 'lastModifiedDate',
              text: 'Last Modified Date'
            }
          ]
        },
        {
          attr: 'pageresults',
          label: 'Page Results',
          type: 'select',
          values: [
            {
              value: 'true',
              text: 'Yes'
            },
            {
              value: 'false',
              text: 'No'
            }
          ]
        },
        {
          attr: 'pagesize',
          label: 'Page Size',
          type: 'select',
          values: ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20']
        }
      ]
    },
    {
      action: "respond.gallery",
      selector: "respond-gallery",
      title: "Gallery",
      display: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"  height="100%" width="100%"><path d="M0 0h24v24H0z" fill="none"/><path d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-11-4l2.03 2.71L16 11l4 5H8l3-4zM2 6v14c0 1.1.9 22 2h14v-2H4V6H2z"/></svg>',
      view: '<div class="respond-plugin"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"  height="100%" width="100%"><path d="M0 0h24v24H0z" fill="none"/><path d="M22 16V4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2zm-11-4l2.03 2.71L16 11l4 5H8l3-4zM2 6v14c0 1.1.9 22 2h14v-2H4V6H2z"/></svg></div>',
      html: '<respond-gallery id="test"></respond-gallery>',
      attributes: [
        {
          attr: 'id',
          label: 'Gallery',
          type: 'select',
          values: ['respond.galleries']
        }
      ]
    }]

  }

})();

// add plugins
if(hashedit.menu !== null && hashedit.menu !== undefined) {
  hashedit.menu = hashedit.menu.concat(respond.core.plugins); 
}