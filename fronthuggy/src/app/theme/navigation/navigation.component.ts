import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';

@Component({
  selector: 'navigation',
  templateUrl: './navigation.component.html',
  styleUrls: ['./navigation.component.css']
})
export class NavigationComponent implements OnInit {
  public user:any = {};
  constructor(public router: Router) {}

  ngOnInit() {
    this.user = (localStorage.getItem('user')) ? JSON.parse(localStorage.getItem('user')) : {};
  }

  activeRoute(routename: string): boolean{
    return this.router.url.indexOf(routename) > -1;
  }
}
