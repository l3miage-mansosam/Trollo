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
   /*myAdmin: any = {
     email: '',
     firstName: '',
     lastName: '',
     password: '',
     role: 'ADMIN',
  };*/

  myUser: any = {
    email: '',
    firstName: '',
    lastName: '',
    password: '',
  }

  registerUser(): void {
    console.log("user", this.myUser);
    this.searchService.registerUser(this.myUser).subscribe({
      next: (response) => {
        if (response.data) {
          this.router.navigate(['/login']);
        } else {
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

    this.searchService.registerUser({...this.myUser,role: 'ADMIN'}).subscribe({
      next: (response) => {
        if (response.data) {
          console.log('reponse:',response);
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
