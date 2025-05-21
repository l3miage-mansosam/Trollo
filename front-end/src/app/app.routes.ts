import { Routes } from '@angular/router';
import { SearchComponent } from './pages/search/search.component';
import { SearchResultComponent } from './pages/search-result/search-result.component';
import { BookTicketComponent } from './pages/book-ticket/book-ticket.component';
import { MyBookingsComponent } from './pages/my-bookings/my-bookings.component';
import { LoginComponent } from './pages/login/login.component';
import { RegisterComponent } from './pages/register/register.component';
import { ScheduleComponent } from './admin/schedule/schedule.component';
import { BookingsComponent } from './admin/bookings/bookings.component';
import { NewLocalisationComponent } from './admin/new-localisation/new-localisation.component';


export const routes: Routes = [

    {
        path: '',
        redirectTo: 'search',
        pathMatch: 'full'
    }
    ,
    {
        path: 'search',
        component:SearchComponent

    },
    {
    path:'search-result/:fromId/:toId/:date',
    component:SearchResultComponent


    }
    ,
    {
        path:'book-ticket/:scheduleId',
        component:BookTicketComponent
    }
    ,
    {
        path:'my-booking',

        component:MyBookingsComponent
    }
    ,
    {
    path:'login',
    component:LoginComponent
    },
    {
        path:'register',
        component:RegisterComponent
        },

        {
        path:'schedule',
        component:ScheduleComponent
        },
        {
            path:'bookings',
            component:BookingsComponent
          
            },
            
            {path:'new-localisation',
                component:NewLocalisationComponent

            }
    

    
];
