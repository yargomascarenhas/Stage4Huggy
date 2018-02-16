import { Component, OnInit } from '@angular/core';
import { ApiService } from '../../global/api.service';

@Component({
  selector: 'app-reports',
  templateUrl: './reports.component.html'
})
export class ReportsComponent implements OnInit {
  public page:string = 'Relatórios';
  public titulo:string = 'Tickets';
  public itensstatus:any = [];
  public itenssatisfaction:any = [];
  public user:any = {};
  public isadmin:boolean = false;

  public filtros:any = {
    id: null,
    tags: null,
    type: null,
    status: null,
    requester_id: null,
    organization_id: null,
    priority: null,
    satisfaction_rating: null
  }

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
		this.api.get('v1/tickets/groupstatus')
		.subscribe((resp) => {
			for(let item of resp.data) {
			  this.itensstatus.push(item);
      }
		},
		(err) => {

    });

    this.api.get('v1/tickets/groupsatisfaction')
		.subscribe((resp) => {
			for(let item of resp.data) {
			  this.itenssatisfaction.push(item);
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
