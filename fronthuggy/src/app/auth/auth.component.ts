import { Router } from '@angular/router';
import { ApiService } from './../global/api.service';
import { Component, OnInit } from '@angular/core';
import swal from 'sweetalert2';

@Component({
  selector: 'app-auth',
  templateUrl: './auth.component.html'
})
export class AuthComponent implements OnInit {

  public forgot:boolean = false;
  public email:string = '';
  public password:string = '';
  public isloading:boolean = false;

  constructor(
    public api: ApiService,
    public router: Router
  ) { }

  ngOnInit() {
    this.logoff();
  }

  public forgotPwd() {
    this.forgot = (this.forgot == true) ? false : true;
  }

  public login() {
    this.isloading = true;
    this.api.post('v1/users/login', {
      login: this.email,
      password: this.password
    })
    .subscribe(
    (res) => {
      this.isloading = false;
      // set localstorage
      localStorage.setItem('token', res.token);
      localStorage.setItem('user', JSON.stringify(res.data[0]));
      // enter
      this.router.navigate(['/home']);
    },
    (err) => {
      this.isloading = false;
      // display error
      swal('Erro', 'Usuário e senha inválidos', 'error');
    });
    return false;
  }

  private logoff() {
    localStorage.clear();
  }

}
