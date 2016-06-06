var plugins = [
  {
        action: "respond.blog",
        selector: "[block][type=posts]",
        title: "Blog Posts",
        display: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="100%" width="100%"><path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z"/><path d="M0 0h24v24H0z" fill="none"/></svg>',
        view: '<div class="respond-plugin"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="100%" width="100%"><path d="M4 14h4v-4H4v4zm0 5h4v-4H4v4zM4 9h4V5H4v4zm5 5h12v-4H9v4zm0 5h12v-4H9v4zM9 5v4h12V5H9z"/><path d="M0 0h24v24H0z" fill="none"/></svg><span>' + hashedit.i18n('Blog Posts') + '</span></div>',
        html: '<div respond-plugin type="posts"></div>'

  },
  {
      action: "respond.form",
      selector: "respond-form",
      title: "Form",
      display: '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="100%" width="100%"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>',
      view: '<div class="respond-plugin"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" height="100%" width="100%"><path d="M0 0h24v24H0z" fill="none"/><path d="M19 3H5c-1.11 0-2 .9-2 2v14c0 1.1.89 2 2 2h14c1.11 0 2-.9 2-2V5c0-1.1-.89-2-2-2zm-9 14l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg><span>' + hashedit.i18n('Form') + '</span></div>',
      html: '<div respond-plugin type="form" form="contact-us"></respond-form>',
      attributes: [
        {
          attr: 'form',
          label: 'Form',
          type: 'select',
          values: ['respond.forms']
        }
      ]
  }
];

// add plugins
if(hashedit.menu !== null && hashedit.menu !== undefined) {
  hashedit.menu = hashedit.menu.concat(plugins);
}