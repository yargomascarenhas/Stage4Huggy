import { AuthGuard } from './auth.guard';
import { HomeComponent } from './home/home.component';
import { NgModule } from "@angular/core";
import { RouterModule, Routes } from "@angular/router";
import { AuthComponent } from "./auth.component";

const routes: Routes = [
    { path: '', component: AuthComponent },
    { path: 'home', component: HomeComponent, canActivate: [AuthGuard] }
];

@NgModule({
    imports: [RouterModule.forChild(routes)],
    exports: [RouterModule]
})
export class AuthRouting {}