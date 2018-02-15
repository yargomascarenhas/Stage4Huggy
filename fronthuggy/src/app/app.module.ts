import { FooterComponent } from './global/footer/footer.component';
import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { BrowserAnimationsModule } from "@angular/platform-browser/animations";
import { AppRouting } from './app.routing';
import { AppComponent } from './app.component';
import { AuthModule } from "./auth/auth.module";
import { HttpClientModule } from '@angular/common/http';
import { HttpModule } from '@angular/http';
import { FormsModule  } from "@angular/forms";
import { NavigationComponent } from './global/navigation/navigation.component';
import { ScreenComponent } from './global/screen/screen.component';
import { TopnavbarComponent } from './global/topnavbar/topnavbar.component';

@NgModule({
  declarations: [
    AppComponent,
    FooterComponent,
    NavigationComponent,
    ScreenComponent,
    TopnavbarComponent
  ],
  imports: [
    BrowserModule,
    FormsModule,
    HttpModule,
    HttpClientModule,
    BrowserAnimationsModule,
    AppRouting,
    AuthModule
  ],
  providers: [],
  bootstrap: [AppComponent]
})
export class AppModule { }