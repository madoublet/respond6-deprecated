import {Component, EventEmitter, Input, Output} from '@angular/core';
import {Router, ROUTER_DIRECTIVES, CanActivate} from '@angular/router-deprecated';
import {TranslatePipe} from 'ng2-translate/ng2-translate';
import {tokenNotExpired} from 'angular2-jwt/angular2-jwt';
import {SiteService} from '/app/shared/services/site.service';

@Component({
    selector: 'respond-drawer',
    templateUrl: './app/shared/components/drawer/drawer.component.html',
    directives: [ROUTER_DIRECTIVES],
    providers: [SiteService],
    pipes: [TranslatePipe]
})

@CanActivate(() => tokenNotExpired())

export class DrawerComponent {

  _visible: boolean = false;

  @Input()
  set visible(visible: boolean){
    this._visible = visible;
  }

  get visible() { return this._visible; }

  @Input()
  set active(active: string){
    this._active = active;
  }

  get active() { return this._active; }


  @Output() onHide = new EventEmitter<any>();

  constructor (private _siteService: SiteService, private _router: Router) {}

  /**
   * Init pages
   */
  ngOnInit() {
    this.id = localStorage.getItem('respond.siteId');
    this.dev = false;
    
    var url = window.location.href;
    
    if(url.indexOf('?dev') !== -1) {
      this.dev = true;
    }
    
  }

  /**
   * Hides the add page modal
   */
  hide() {
    this._visible = false;
    this.onHide.emit(null);
  }

  /**
   * Reload system files
   */
  reload() {

    this._siteService.reload()
                     .subscribe(
                       data => { toast.show('success'); },
                       error => { toast.show('failure');  }
                      );

  }

  /**
   * Signs out of the system
   */
  signOut() {

    // remove token
    localStorage.removeItem('respond.siteId');

    // redirect
    this._router.navigate( ['Login', {id: this.id}] );

  }

}