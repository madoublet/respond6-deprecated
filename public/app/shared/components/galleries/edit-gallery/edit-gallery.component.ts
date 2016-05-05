import {Component, EventEmitter, Input, Output} from '@angular/core';
import {CanActivate} from '@angular/router-deprecated';
import {tokenNotExpired} from 'angular2-jwt/angular2-jwt';
import {GalleryService} from '/app/shared/services/gallery.service';

@Component({
    selector: 'respond-edit-gallery',
    templateUrl: './app/shared/components/galleries/edit-gallery/edit-gallery.component.html',
    providers: [GalleryService]
})

@CanActivate(() => tokenNotExpired())

export class EditGalleryComponent {

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
  set gallery(gallery){

    // set visible
    this.model = gallery;

  }

  @Output() onCancel = new EventEmitter<any>();
  @Output() onUpdate = new EventEmitter<any>();
  @Output() onError = new EventEmitter<any>();

  constructor (private _galleryService: GalleryService) {}

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
   * Submits the gallery
   */
  submit() {

    this._galleryService.edit(this.model.id, this.model.name)
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