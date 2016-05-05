import {Component, EventEmitter, Input, Output} from '@angular/core'
import {ROUTER_DIRECTIVES, CanActivate} from '@angular/router-deprecated'
import {tokenNotExpired} from 'angular2-jwt/angular2-jwt'
import {SiteService} from '/app/shared/services/site.service'

@Component({
    selector: 'respond-drawer',
    templateUrl: './app/shared/components/drawer/drawer.component.html',
    directives: [ROUTER_DIRECTIVES],
    providers: [SiteService]
})

@CanActivate(() => tokenNotExpired())

export class DrawerComponent {

  _visible: boolean = false;

  @Input()
  set visible(visible: boolean){
    this._visible = visible;
  }

  get visible() { return this._visible; }

  @Output() onHide = new EventEmitter<any>();

  constructor (private _siteService: SiteService) {}

  /**
   * Init pages
   */
  ngOnInit() { }

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

}