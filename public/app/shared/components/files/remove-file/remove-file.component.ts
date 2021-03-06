import {Component, EventEmitter, Input, Output} from '@angular/core';
import {CanActivate} from '@angular/router-deprecated';
import {TranslatePipe} from 'ng2-translate/ng2-translate';
import {tokenNotExpired} from 'angular2-jwt/angular2-jwt';
import {FileService} from '/app/shared/services/file.service';

@Component({
    selector: 'respond-remove-file',
    templateUrl: './app/shared/components/files/remove-file/remove-file.component.html',
    providers: [FileService],
    pipes: [TranslatePipe]
})

@CanActivate(() => tokenNotExpired())

export class RemoveFileComponent {

  routes;
  errorMessage;

  // model to store
  model: {
    name: '',
    url: ''
  };

  _visible: boolean = false;

  @Input()
  set visible(visible: boolean){

    // set visible
    this._visible = visible;

  }

  @Input()
  set file(file){

    // set visible
    this.model = file;

  }

  get visible() { return this._visible; }

  @Output() onCancel = new EventEmitter<any>();
  @Output() onUpdate = new EventEmitter<any>();
  @Output() onError = new EventEmitter<any>();

  constructor (private _fileService: FileService) {}

  /**
   * Init files
   */
  ngOnInit() {

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

    this._fileService.remove(this.model.name)
                     .subscribe(
                       data => { this.success(); },
                       error => { this.onError.emit(<any>error) }
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