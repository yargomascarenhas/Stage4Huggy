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
  public filters:any = [];
	public endpoint:string = 'v1/tickets';
  private next:string = this.endpoint;
  public hasnext:boolean = false;

  constructor(
    public api: ApiService
  ) { }

  ngOnInit() {
    this.loadItens();
  }

  public loadItens() {
    // let loading:any;
		// if(this.first_time) {
    //     	loading = this.loadreq.trowload();
		// }
		this.api.get(this.next)
		.subscribe((resp) => {
      console.log(resp);
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

}
