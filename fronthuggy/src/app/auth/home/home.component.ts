import { Component, OnInit } from '@angular/core';
import { ApiService } from '../../global/api.service';

@Component({
  selector: 'app-home',
  templateUrl: './home.component.html'
})
export class HomeComponent implements OnInit {
  public page:string = 'Home';
  public titulo:string = 'Tickets';
  public itens:any = [];
	public endpoint:string = 'v1/tickets';
  private next:string = this.endpoint;
  public hasnext:boolean = false;
  public tipos:any = [{id: null, name: 'Tipo'}];
  public statuslist:any = [{id: null, name: 'Status'}];
  public requesterlist: any = [{id: null, name: 'Solicitante'}];
  public organizationlist: any = [{id: null, name: 'Empresa'}];
  public prioritylist:any = [{id: null, name: 'Prioridade'}];
  public satisfactionlist:any = [{id:null, name: 'Ind. Satisfação'}];
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
    // let loading:any;
		// if(this.first_time) {
    //     	loading = this.loadreq.trowload();
		// }
		this.api.get(this.next)
		.subscribe((resp) => {
			for(let item of resp.data) {

        if(!this.exists(this.tipos, item.type, 'id') && item.type) {
          this.tipos.push({id: item.type, name: item.type});
        }
        if(!this.exists(this.statuslist, item.status, 'id') && item.status) {
          this.statuslist.push({id: item.status, name: item.status});
        }
        if(!this.exists(this.requesterlist, item.requester_id, 'id') && item.requester_id) {
          this.requesterlist.push({id: item.requester_id, name: item.requester_name});
        }
        if(!this.exists(this.organizationlist, item.organization_id, 'id') && item.organization_id) {
          this.organizationlist.push({id: item.organization_id, name: item.organization_name});
        }
        if(!this.exists(this.prioritylist, item.priority, 'id') && item.priority) {
          this.prioritylist.push({id: item.priority, name: item.priority});
        }
        if(!this.exists(this.satisfactionlist, item.satisfaction_rating, 'id') && item.satisfaction_rating) {
          this.satisfactionlist.push({id: item.satisfaction_rating, name: item.satisfaction_rating});
        }
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

  private filter() {
    console.log(this.filtros);
    let filterstr:string = '';
    let includefilter = function(key:string, value:string) {
      let strfil:string = '';
      if(value != null && value != 'null') {
        strfil += (filterstr == '') ? '?'+key+'=' + value : '&'+key+'=' + value;
      }
      return strfil;
    }

    filterstr += includefilter('id', this.filtros.id);
    filterstr += includefilter('tags', this.filtros.tags);
    filterstr += includefilter('type', this.filtros.type);
    filterstr += includefilter('status', this.filtros.status);
    filterstr += includefilter('requester_id', this.filtros.requester_id);
    filterstr += includefilter('organization_id', this.filtros.organization_id);
    filterstr += includefilter('priority', this.filtros.priority);

    this.next = this.endpoint + filterstr;

    console.log(this.next);
    this.itens = [];
    this.loadItens();
  }

  private exists(lista:any, value:any, idx?:string) {
    let exist = lista.filter((dat) => {
      return (idx) ? dat[idx] == value : dat == value;
    });
    return (!exist[0]) ? false : true;
  }
}
