import { Component, OnInit } from '@angular/core';
import { ApiService } from '../../global/api.service';

@Component({
  selector: 'app-users',
  templateUrl: './users.component.html'
})
export class UsersComponent implements OnInit {
  public page:string = 'Usuários';
  public titulo:string = 'Usuários';
  public itens:any = [];
	public endpoint:string = 'v1/users';
  private next:string = this.endpoint;
  public hasnext:boolean = false;
  public user:any = {};
  public isadmin:boolean = false;

  constructor(
    public api: ApiService
  ) { }

  ngOnInit() {
    this.loadItens();
    this.user = (localStorage.getItem('user')) ? JSON.parse(localStorage.getItem('user')) : {};
    if(this.user.perfil) {
      this.isadmin = (this.user.perfil == 'admin') ? true : false;
    }
  }

  public loadItens() {
    // let loading:any;
		// if(this.first_time) {
    //     	loading = this.loadreq.trowload();
		// }
		this.api.get(this.next)
		.subscribe((resp) => {
			for(let item of resp.data) {
			  this.itens.push(item);
      }

      this.hasnext = (resp._links.next) ? true : false;
			if(resp._links.next) {
				this.next = resp._links.next;
			}
		},
		(err) => {

    });
  }

  private exists(lista:any, value:any, idx?:string) {
    let exist = lista.filter((dat) => {
      return (idx) ? dat[idx] == value : dat == value;
    });
    return (!exist[0]) ? false : true;
  }
}
