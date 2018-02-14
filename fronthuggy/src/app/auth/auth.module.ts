import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthRouting } from './auth.routing';
import { AuthComponent } from './auth.component';

@NgModule({
  imports: [
    CommonModule,
    AuthRouting
  ],
  declarations: [AuthComponent]
})
export class AuthModule { }
