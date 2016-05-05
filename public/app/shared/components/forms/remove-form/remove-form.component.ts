import {Component, EventEmitter, Input, Output} from '@angular/core';
import {CanActivate} from '@angular/router-deprecated';
import {tokenNotExpired} from 'angular2-jwt/angular2-jwt';
import {FormService} from '/app/shared/services/form.service';

@Component({
    selector: 'respond-remove-form',
    templateUrl: './app/shared/components/forms/remove-form/remove-form.component.html',
    providers: [FormService]
})

@CanActivate(() => tokenNotExpired())

export class RemoveFormComponent {

  routes;

  // model to store
  model;

  _visible: boolean = false;

  // visible input
  @Input()
  set visible(visible: boolean){

    // set visible
    this._visible = visible;

  }

  get visible() { return this._visible; }

  // form input
  @Input()
  set form(form){

    // set visible
    this.model = form;

  }

  // outputs
  @Output() onCancel = new EventEmitter<any>();
  @Output() onUpdate = new EventEmitter<any>();
  @Output() onError = new EventEmitter<any>();

  constructor (private _formService: FormService) {}

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

    this._formService.remove(this.model.id)
                     .subscribe(
                       data => { this.success(); },
                       error =>  { this.onError.emit(<any>error); }
                      );

  }

  /**
   * Handles a successful submission
   */
  success() {

    this._visible = false;
    this.onUpdate.emit(null);

  }


}