import {Component, EventEmitter, Input, Output} from '@angular/core';
import {CanActivate} from '@angular/router-deprecated'
import {tokenNotExpired} from 'angular2-jwt/angular2-jwt'
import {MenuService} from '/app/shared/services/menu.service'

@Component({
    selector: 'respond-edit-menu',
    templateUrl: './app/shared/components/menus/edit-menu/edit-menu.component.html',
    providers: [MenuService]
})

@CanActivate(() => tokenNotExpired())

export class EditMenuComponent {

  routes;

  // model to store
  model;

  _visible: boolean = false;

  @Input()
  set visible(visible: boolean){

    // set visible
    this._visible = visible;

  }

  get visible() { return this._visible; }

  @Input()
  set menu(menu){

    // set visible
    this.model = menu;

  }

  @Output() onCancel = new EventEmitter<any>();
  @Output() onUpdate = new EventEmitter<any>();
  @Output() onError = new EventEmitter<any>();

  constructor (private _menuService: MenuService) {}

  /**
   * Init
   */
  ngOnInit() {
    this.model = {
      id: '',
      name: ''
    };
  }

  /**
   * Hides the modal
   */
  hide() {
    this._visible = false;
    this.onCancel.emit(null);
  }

  /**
   * Submits the form
   */
  submit() {

    this._menuService.edit(this.model.id, this.model.name)
                     .subscribe(
                       data => { this.success(); },
                       error =>  { this.onError.emit(<any>error); }
                      );

  }

  /**
   * Handles a successful edit
   */
  success() {

    toast.show('success');

    this._visible = false;
    this.onUpdate.emit(null);

  }

}