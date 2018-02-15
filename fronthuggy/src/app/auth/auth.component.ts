import { ApiService } from './../global/api.service';
import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-auth',
  templateUrl: './auth.component.html'
})
export class AuthComponent implements OnInit {

  public forgot:boolean = false;
  public email:string = '';
  public password:string = '';

  constructor(
    public api: ApiService
  ) { }

  ngOnInit() {
  }

  public forgotPwd() {
    this.forgot = (this.forgot == true) ? false : true;
  }

  public login() {
    this.api.post('v1/users/login', {
      login: this.email,
      password: this.password
    })
    .subscribe(
    (res) => {
      console.log(res);
      // set localstorage
      // enter
    },
    (err) => {
      console.error(err);
      // display error
    });
    return false;
  }

}
