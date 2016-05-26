import {Component} from '@angular/core';
import {SiteService} from '/app/shared/services/site.service';
import {TranslatePipe} from 'ng2-translate/ng2-translate';

@Component({
    selector: 'respond-create',
    templateUrl: './app/create/create.component.html',
    providers: [SiteService],
    pipes: [TranslatePipe]
})

export class CreateComponent {

  model;
  site;
  errorMessage;

  constructor (private _siteService: SiteService) {}

  /**
   * Init pages
   */
  ngOnInit() {

    this.model = {
      name: '',
      theme: '',
      email: '',
      password: '',
      passcode: ''
    };

  }

  /**
   * Create the site
   *
   */
  submit() {

      this._siteService.create(this.model.name, this.model.theme, this.model.email, this.model.password, this.model.passcode)
                   .subscribe(
                     data => { this.site = data; this.success(); },
                     error =>  { this.failure(<any>error); }
                    );

  }

  /**
   * Handles a successful create
   *
   */
  success() {

    alert('success! site=' + this.site.id);

    // clear model
    this.model = {
      name: '',
      theme: '',
      email: '',
      password: '',
      passcode: ''
    };

  }

  /**
   * handles errors
   */
  failure(obj) {

    toast.show('failure');

  }

}