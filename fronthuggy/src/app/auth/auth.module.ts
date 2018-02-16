import { ThemeModule } from './../theme/theme.module';
import { HomeComponent } from './home/home.component';
import { AuthGuard } from './auth.guard';
import { FormsModule } from '@angular/forms';
import { ApiService } from './../global/api.service';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthRouting } from './auth.routing';
import { AuthComponent } from './auth.component';

@NgModule({
  imports: [
    ThemeModule,
    CommonModule,
    AuthRouting,
    FormsModule
  ],
  declarations: [
    AuthComponent,
    HomeComponent
  ],
  providers: [ApiService, AuthGuard]
})
export class AuthModule { }
