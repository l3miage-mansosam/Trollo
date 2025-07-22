import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { RouterOutlet } from '@angular/router';
import { User } from './model/model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms'; 
import { OnInit } from '@angular/core';
//RouterModule
// import { NgModule } from '@angular/core';
import { RouterModule } from '@angular/router';
import { NavigationEnd } from '@angular/router';
import { filter } from 'rxjs';
import { Event } from '@angular/router';
import { SearchComponent } from "./pages/search/search.component";
import { HttpClient } from '@angular/common/http';


@Component({
  selector: 'app-root',
  imports: [RouterOutlet, CommonModule, FormsModule, RouterModule],

  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent implements OnInit {
    topRoutes: any[] = [];
  locationMap: Map<string, number> = new Map();
  title = 'BusBooking';
  user: User ;
  showLoginPopup: boolean = false;
  authMode: 'login' | 'register' = 'login';
    currentYear: number = new Date().getFullYear();


   constructor(private route: Router, private http: HttpClient) {
    this.user = new User();
    const stored = localStorage.getItem('user');
    if (stored) {
      this.user = JSON.parse(stored);
    }
  }
  ngOnInit() {
    // à chaque fin de navigation, on recharge le user
    this.route.events
      .pipe(
        filter((evt: Event): evt is NavigationEnd => evt instanceof NavigationEnd)
      )
      .subscribe(() => {
        const storedUser = localStorage.getItem('user');
        this.user = storedUser ? JSON.parse(storedUser) : new User();
      });
       this.loadLocations();
          this.loadTopRoutes();

  }
 isLoggedIn(): boolean {
    return !!localStorage.getItem('user');
  }
  logout(): void {
    localStorage.removeItem('user');
    this.user = new User();
    this.route.navigate(['/login']);
  }
  navigateLogin(): void {
    this.route.navigate(['/login']);
  }
  navigateRegister(): void {
    this.route.navigate(['/register']);
  }
  navigateVendorSchedule(): void { 
    this.route.navigate(['/schedule']);
  
  }
  navigateUserDashboard(): void {
    
  }
    isHomePage(): boolean {
    return this.route.url === '/search' || this.route.url === '/';
  }
loadTopRoutes(): void {
  this.http.get<any[]>('https://api.freeprojectapi.com/api/BusBooking/GetAvailableRoutes')
    .subscribe({
      next: (data) => {
        const seen = new Set();
        const uniqueRoutes = data.filter(route => {
          const key = `${route.fromLocationName}-${route.toLocationName}`;
          if (seen.has(key)) {
            return false;
          } else {
            seen.add(key);
            return true;
          }
        });
        this.topRoutes = uniqueRoutes.slice(0, 5);
      },
      error: err => console.error('Error loading routes', err)
    });
}

loadLocations(): void {
    this.http.get<any[]>('https://api.freeprojectapi.com/api/BusBooking/GetBusLocations')
      .subscribe({
        next: (locations) => {
          locations.forEach(loc => {
            this.locationMap.set(loc.locationName, loc.locationId);
          });
        },
        error: err => console.error('Error loading locations', err)
      });
  }
  searchBusByName(fromName: string, toName: string, date: string): void {
    const fromId = this.locationMap.get(fromName);
    const toId = this.locationMap.get(toName);
    if (fromId && toId) {
      this.route.navigate(['/search-result', fromId, toId, date]);
    } else {
      console.error('One or both locations not found');
    }
  }
}