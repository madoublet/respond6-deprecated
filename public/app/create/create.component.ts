import {Component} from '@angular/core';
import {SiteService} from '/app/shared/services/site.service';
import {AppService} from '/app/shared/services/app.service';
import {TranslatePipe} from 'ng2-translate/ng2-translate';

@Component({
    selector: 'respond-create',
    templateUrl: './app/create/create.component.html',
    providers: [SiteService, AppService],
    pipes: [TranslatePipe]
})

export class CreateComponent {

  themes;
  visible;
  selectedTheme;
  selectedThemeIndex;
  model;
  site;
  errorMessage;

  constructor (private _siteService: SiteService, private _appService: AppService) {}

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

    this.list();

  }

  /**
   * Create the site
   *
   */
  submit() {

      this._siteService.create(this.model.name, this.selectedTheme.location, this.model.email, this.model.password, this.model.passcode)
                   .subscribe(
                     data => { this.site = data; this.success(); },
                     error =>  { this.failure(<any>error); }
                    );

  }

  /**
   * Updates the list
   */
  list() {

    // list themes in the app
    this._appService.listThemes()
                     .subscribe(
                       data => {
                         this.themes = data;
                         this.selectedTheme = this.themes[0];
                         this.selectedThemeIndex = 0;
                         this.visible = false;
                       },
                       error =>  { this.failure(<any>error); }
                      );
  }

  /**
   * Cycles through themes
   */
  next () {

    // increment or cycle
    if((this.selectedThemeIndex + 1) < this.themes.length) {
      this.selectedThemeIndex = this.selectedThemeIndex + 1;
    }
    else {
      this.selectedThemeIndex = 0;
    }

    // set new theme
    this.selectedTheme = this.themes[this.selectedThemeIndex];

  }

  /**
   * Uses the selected theme
   */
  useTheme () {

    // set new theme
    this.visible = true;

  }

  /**
   * Hides the create modal
   */
  hide () {

    // set new theme
    this.visible = false;

  }

  /**
   * Handles a successful create
   *
   */
  success() {

    toast.show('success');

    this._router.navigate( ['Login', {id: this.site.id}] );

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