import {Injectable}     from '@angular/core'
import {Http, Response} from '@angular/http'
import {Headers, RequestOptions} from '@angular/http'
import {Observable} from 'rxjs/Observable'

@Injectable()
export class AppService {
  constructor (private http: Http) {}

  private _themesListUrl = 'api/themes/list';
  private _languagesListUrl = 'api/languages/list';

  /**
   * Lists themes in the application
   *
   */
  listThemes () {

    return this.http.get(this._themesListUrl).map((res:Response) => res.json());

  }


}