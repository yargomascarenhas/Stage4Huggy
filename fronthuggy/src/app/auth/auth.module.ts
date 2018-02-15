import { FormsModule } from '@angular/forms';
import { ApiService } from './../global/api.service';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthRouting } from './auth.routing';
import { AuthComponent } from './auth.component';

@NgModule({
  imports: [
    CommonModule,
    AuthRouting,
    FormsModule
  ],
  declarations: [AuthComponent],
  providers: [ApiService]
})
export class AuthModule { }
