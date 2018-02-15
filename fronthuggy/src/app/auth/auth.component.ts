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
    console.log(this.email, this.password);
    return false;
  }

}
