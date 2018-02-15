import { ThemeModule } from './theme/theme.module';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { PagesComponent } from './pages.component';

@NgModule({
  imports: [
    CommonModule,
    ThemeModule
  ],
  declarations: [PagesComponent]
})
export class PagesModule { }
