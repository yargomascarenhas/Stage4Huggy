import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { Router, NavigationStart, NavigationEnd, RouterOutlet } from '@angular/router';
// import { Helpers } from "./helpers";

@Component({
    selector: 'app-root',
    templateUrl: './app.component.html',
    encapsulation: ViewEncapsulation.None,
})
export class AppComponent implements OnInit {
    title = 'app';

    constructor(private _router: Router) {
    }

    ngOnInit() {
        this._router.events.subscribe((route) => {
            if (route instanceof NavigationStart) {
                // Helpers.setLoading(true);
                // Helpers.bodyClass(this.globalBodyClass);
            }
            if (route instanceof NavigationEnd) {
                // Helpers.setLoading(false);
            }
        });
    }
}