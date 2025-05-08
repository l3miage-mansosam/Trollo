import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { SearchService } from '../../service/search.service';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  userName: string = '';
  password: string = '';
  errorMessage: string = '';

  constructor(private searchService: SearchService, private router: Router) {}

  login(): void {
    console.log("login", this.userName, this.password);
    this.searchService.loginUser({ userName: this.userName, password: this.password }).subscribe({
      next: (response) => {
        if (response.result) {
          localStorage.setItem('user', JSON.stringify(response.data));
          this.router.navigate(['/']);
        } else {
          this.errorMessage = response.message;
        }
      },
      error: () => {
        this.errorMessage = 'An error occurred during login.';
      }
    });
  }
}