import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { SearchService } from '../../service/search.service';
import { FormsModule } from '@angular/forms';
import {User} from '../../model/model';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css']
})
export class LoginComponent {
  email: string = '';
  password: string = '';
  errorMessage: string = '';
  token: string = '';

  user: User | null = null;

  constructor(private searchService: SearchService, private router: Router) {}

  login(): void {
    console.log("Tentative de connexion...", this.email, this.password);

    // Vérifier s'il y a une URL de redirection
    const redirectUrl = localStorage.getItem('redirectUrl');

    // Appel à l'API de login
    this.searchService.loginUser({ email: this.email, password: this.password }).subscribe({
      next: (response) => {
        if (response?.token) {
          // Stocker le token retourné par l'API
          this.token = response?.token;
          localStorage.setItem('userToken', this.token);

          // Appel à l'API /me pour récupérer les informations de l'utilisateur
          this.searchService.getUserByToken(this.token).subscribe({
            next: (user) => {
              this.user = user; // Stocker l'utilisateur dans une variable
              console.log("Utilisateur connecté :", this.user);

              localStorage.setItem('user',JSON.stringify(this.user));

              // Gestion de la redirection après connexion
              if (redirectUrl) {
                localStorage.removeItem('redirectUrl');
                window.location.href = decodeURIComponent(redirectUrl);
              } else {
                this.router.navigateByUrl('/');
              }
            },
            error: (err) => {
              console.error("Erreur lors de la récupération des informations utilisateur :", err);
              this.errorMessage = "Impossible de récupérer vos informations utilisateur.";
            }
          });
        } else {
          this.errorMessage = 'Email ou mot de passe incorrect.';
        }
      },
      error: (err) => {
        console.error("Erreur lors de la connexion :", err);
        this.errorMessage = "Une erreur est survenue lors de la tentative de connexion.";
      }
    });
  }
}
