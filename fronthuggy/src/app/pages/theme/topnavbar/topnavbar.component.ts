import { Component, OnInit } from '@angular/core';
import { smoothlyMenu } from '../../../app.helpers';
declare var jQuery:any;

@Component({
  selector: 'topnavbar',
  templateUrl: './topnavbar.component.html',
  styleUrls: ['./topnavbar.component.css']
})
export class TopnavbarComponent implements OnInit {

  constructor() { }

  ngOnInit() {
  }

  public toggleNavigation(){
    jQuery("body").toggleClass("mini-navbar");
    smoothlyMenu();
  }

}
