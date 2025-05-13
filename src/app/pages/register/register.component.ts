import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { SearchService } from '../../service/search.service';
import { User } from '../../model/model';

import { NgModel } from '@angular/forms'; 
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [FormsModule,CommonModule],
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css']
  
})
export class RegisterComponent {
  user: User = new User();
  errorMessage: string = '';
  showVendorForm: boolean = false;
  vendor = {
    companyName: '',
    companyEmail: '',
    companyPhone: '',
    companyAddress: ''
  };
  confirmPassword: string = '';

  constructor(private searchService: SearchService, private router: Router) {

  }

  registerUser(): void {
    this.searchService.registerUser(this.user).subscribe({
      next: (response) => {
        if (response.result) {
          this.router.navigate(['/login']);
        } else {
          console.log("user", this.user);
          console.log("response", response);
          this.errorMessage = response.message;
        }
      },
      error: () => {
        this.errorMessage = 'An error occurred during registration.';
      }
    });
  }
  showVendorFormFunction() {
    this.showVendorForm = true;
  }

  // Fonction pour afficher le formulaire de l'utilisateur
  showUserFormFunction() {
    this.showVendorForm = false;
  }
  registerVendor() {
    console.log('Vendor Registration:', this.vendor);
  }
  navigateToLogin() {
    this.router.navigate(['/login']);
  }
}
