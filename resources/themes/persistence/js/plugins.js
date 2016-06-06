var respond = respond || {};

/**
 * Handles plugins functionality for Respond
 *
 */
respond.plugins = {

	init:function(){

  },

  submitForm: function(e) {

    // get reference to form
    var form = e.target.parentNode;


		// select all inputs in the local DOM
		var groups = form.querySelectorAll('.form-group');

		// get page information
		var siteId = respond.site.settings.id;
		var api = respond.site.settings.api;

		// create submission
		var submission = {
			url: window.location.href,
			siteId: siteId,
			formId: this.id,
			fields: []
		};

		// walk through inputs
		for(x=0; x<groups.length; x++) {

			// get name, id, type
			var label = groups[x].getAttribute('data-label');
			var id = groups[x].getAttribute('data-id');
			var type = groups[x].getAttribute('data-type');
			var required = groups[x].getAttribute('data-required');

			// get value by type
			var value = '';

			if(type == 'text'){
				value = groups[x].querySelector('input').value;
			}
			else if(type == 'textarea'){
				value = groups[x].querySelector('textarea').value;
			}
			else if(type == 'radiolist'){
				var radio = groups[x].querySelector('input[type=radio]:checked');

				if(radio != null){
					value = radio.value;
				}
			}
			else if(type == 'select'){
				value = groups[x].querySelector('select').value;
			}
			else if(type == 'checkboxlist'){
				var checkboxes = groups[x].querySelectorAll('input[type=checkbox]:checked');

				// create comma separated list
				for(y=0; y<checkboxes.length; y++){
					value += checkboxes[y].value + ', ';
				}

				// remove trailing comma and space
				if(value != ''){
					value = value.slice(0, -2);
				}
			}

			submission.fields.push(
			  {
				  id: id,
				  value: value
			  }
			);

			// check required fields
			if(required == 'true' && value == ''){
				this.showError = true;
				groups[x].className += ' has-error';
			}

		}

		// exit if error
		if(this.showError == true) {
			return false;
		}

		// set loading
		this.loading = true;

		// set context
		var context = this;

		// submit form
		var xhr = new XMLHttpRequest();

		// set URI
		var uri = api + '/submissions/add';

		xhr.open('POST', encodeURI(uri));

		// handle success
		xhr.onload = function() {
		    if(xhr.status === 200){
		    	// clear form, hide loading
		    	context.loading = false;
		    	context.showSuccess = true;
		    	context.clearForm();
		    }
		    else if(xhr.status !== 200){
		    	context.loading = false;
		        console.log('[respond.error] respond-form component: failed post, xhr.status='+xhr.status);
		    }
		};

		// send serialized data
		xhr.send(JSON.stringify(submission));
  }

};

// fire init
document.addEventListener("DOMContentLoaded", function(event) {
  respond.plugins.init();
});

