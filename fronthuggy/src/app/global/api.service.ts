import { environment } from './../../environments/environment.prod';
import { Injectable } from '@angular/core';
import { Http, RequestOptions, Headers } from '@angular/http';
import 'rxjs/add/operator/map';

@Injectable()
export class ApiService {
  public url: string = environment.api;
  public options: any;

  constructor(
    public http: Http
  ) {
    this.resetHeaders();
  }

  public resetHeaders() {
    let token: string = JSON.parse(localStorage.getItem('token'));
    if(token) {
        let headers = new Headers();
        headers.append('Content-Type', 'application/json');
        headers.append('Authorization', 'Bearer ' + token);
        this.options = new RequestOptions({headers : headers});
    } else {
        // this.events.publish('system:logoff', {}, Date.now());
    }
  }

  public get(endpoint: string, loading: boolean = true) {
      return this.http.get(this.url + '/' + endpoint, this.options).map(resp => resp.json());
  }

  public post(endpoint: string, body: any, loading: boolean = true) {
      return this.http.post(this.url + '/' + endpoint, body, this.options).map(resp => resp.json());
  }

  public put(endpoint: string, body: any, loading: boolean = true) {
      return this.http.put(this.url + '/' + endpoint, body, this.options).map(resp => resp.json());
  }

  public delete(endpoint: string, body: any) {
      return this.http.delete(this.url + '/' + endpoint, this.options).map(resp => resp.json());
  }

  public patch(endpoint: string, body: any) {
      return this.http.patch(this.url + '/' + endpoint, body, this.options).map(resp => resp.json());
  }
}