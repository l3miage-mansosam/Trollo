import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { RouterOutlet } from '@angular/router';
import { User } from './model/model';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms'; 

@Component({
  selector: 'app-root',
  imports: [RouterOutlet,CommonModule, FormsModule],

  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent   {
  
  title = 'BusBooking';
  user: User ;
  showLoginPopup: boolean = false;
  authMode: 'login' | 'register' = 'login';

  constructor(private route: Router) {
    this.user = new User(); // Initialisation par défaut
    const storedUser = localStorage.getItem('user');
    console.log("User from local storage2: ", storedUser);
    if (storedUser) {
      this.user = JSON.parse(storedUser);
      console.log("User from local storage3: ", this.user);
    }    
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
}