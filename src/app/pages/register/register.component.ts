import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { SearchService } from '../../service/search.service';
import { User } from '../../model/model';
//ngmodel
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-register',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './register.component.html',
  
})
export class RegisterComponent {
  user: User = new User();
  errorMessage: string = '';

  constructor(private searchService: SearchService, private router: Router) {}

  register(): void {
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
}
