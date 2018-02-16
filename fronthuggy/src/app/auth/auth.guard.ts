import { Injectable } from "@angular/core";
import { ActivatedRouteSnapshot, CanActivate, Router, RouterStateSnapshot } from "@angular/router";
import { Observable } from "rxjs/Rx";

@Injectable()
export class AuthGuard implements CanActivate {

    constructor(private _router: Router) {
    }

    canActivate(route: ActivatedRouteSnapshot, state: RouterStateSnapshot): Observable<boolean> | boolean {
        let token = localStorage.getItem('token');
        if(token) {
            return true;
        } else {
            this._router.navigate(['login']);
            return false;
        }
    }
}