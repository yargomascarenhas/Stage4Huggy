import { TopnavbarComponent } from '../theme/topnavbar/topnavbar.component';
import { ScreenComponent } from '../theme/screen/screen.component';
import { NavigationComponent } from '../theme/navigation/navigation.component';
import { FooterComponent } from '../theme/footer/footer.component';
import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';

@NgModule({
    declarations: [
        FooterComponent,
        NavigationComponent,
        ScreenComponent,
        TopnavbarComponent
    ],
    exports: [
        FooterComponent,
        NavigationComponent,
        ScreenComponent,
        TopnavbarComponent
    ],
    imports: [
        CommonModule,
        RouterModule,
    ]
})
export class ThemeModule {
}