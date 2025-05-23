import { Component } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import {ApiResponse} from '../../model/model';

@Component({
  selector: 'app-new-localisation',
  imports: [CommonModule, FormsModule],
  templateUrl: './new-localisation.component.html',
  styleUrl: './new-localisation.component.css'

})
export class NewLocalisationComponent {

 locationName: string = '';
  pays: string = '';
  locations: any[] = [];
  successMessage = '';
  errorMessage = '';

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.fetchAllLocations();
  }

  fetchAllLocations() {
    const token = localStorage.getItem('userToken');
    if (!token) {
      this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });

    this.http.get<ApiResponse<any>>('http://localhost:8000/api/cities', {headers})
      .subscribe({
        next: (locations: ApiResponse<any>) => {
          this.successMessage = locations?.message;
          this.locations = locations?.data;
        },
        error: () => {
          this.errorMessage = 'Error adding location. Please try again.';
        }
      });
  }

  submitLocation() {
    this.successMessage = '';
    this.errorMessage = '';

    const newLocation = {
      name: this.locationName,
      pays: this.pays
    };
    console.log(newLocation);
    const token = localStorage.getItem('userToken');
    if (!token) {
      this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });

    this.http.post<ApiResponse<any>>('http://localhost:8000/api/cities', newLocation, {headers})
      .subscribe({
        next: (location: ApiResponse<any>) => {
          this.successMessage = location?.message;
          // this.locationName = location?.name;
          // this.pays = location?.pays;
          this.fetchAllLocations();
        },
        error: () => {
          this.errorMessage = 'Error adding location. Please try again.';
        }
      });
  }
}
