import {Injectable}     from 'angular2/core'
import {Http, Response} from 'angular2/http'
import {Headers, RequestOptions} from 'angular2/http'
import {Observable} from 'rxjs/Observable'

@Injectable()
export class SiteService {
  constructor (private http: Http) {}

  private _createUrl = 'api/sites/create';

  /**
   * Login to the application
   *
   * @param {string} id The site id
   * @param {string} email The user's login email
   * @param {string} password The user's login password
   * @return {Observable}
   */
  create (name: string, theme: string, email: string, password: string, passcode: string) {

    let body = JSON.stringify({ name, theme, email, password, passcode });
    let headers = new Headers({ 'Content-Type': 'application/json' });
    let options = new RequestOptions({ headers: headers });

    return this.http.post(this._createUrl, body, options)
                    .map((res:Response) => res.json());

  }


}