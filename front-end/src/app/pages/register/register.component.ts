import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { SearchService } from '../../service/search.service';
import { User, Vendor } from '../../model/model';

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
  //vendor:Vendor = new Vendor();
  errorMessage: string = '';
  showVendorForm: boolean = false;

  confirmPassword: string = '';

  constructor(private searchService: SearchService, private router: Router) {

  }
   monVendor: any = {
  vendorId: 0,
  vendorName: this.user.userId,
  contactNo: '',
  emailId: this.user.email,
};

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
   registerVendor(): void {
          console.log("vendor", this.user);

    this.searchService.registerVendor(this.user).subscribe({
      next: (response) => {
        if (response.result) {
          this.searchService.postBusVendor(this.monVendor).subscribe({
            next: (response) => {
              localStorage.setItem('monVendor', JSON.stringify(this.monVendor));
              console.log("response", response);
            }
          });

          this.router.navigate(['/login']);
        } else {

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

  navigateToLogin() {
    this.router.navigate(['/login']);
  }
}
