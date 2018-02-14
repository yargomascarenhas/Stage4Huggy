import { Component, OnInit } from '@angular/core';

@Component({
  selector: 'app-auth',
  templateUrl: './auth.component.html'
})
export class AuthComponent implements OnInit {

  public forgot:boolean = false;

  constructor() { }

  ngOnInit() {
  }

  public forgotPwd() {
    this.forgot = (this.forgot == true) ? false : true;
  }

}
