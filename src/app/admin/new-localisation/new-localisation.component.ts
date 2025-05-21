import { Component } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';

@Component({
  selector: 'app-new-localisation',
  imports: [CommonModule, FormsModule],
  templateUrl: './new-localisation.component.html',
  styleUrl: './new-localisation.component.css'

})
export class NewLocalisationComponent {

 locationName: string = '';
  code: string = '';
  locations: any[] = [];
  successMessage = '';
  errorMessage = '';

  constructor(private http: HttpClient) {}

  ngOnInit(): void {
    this.fetchLocations();
  }

  fetchLocations() {
    this.http.get<any[]>('https://api.freeprojectapi.com/api/BusBooking/GetBusLocations')
      .subscribe(data => this.locations = data);
  }

  locationExists(name: string): boolean {
    return this.locations.some(loc => loc.locationName.toLowerCase() === name.toLowerCase());
  }

  submitLocation() {
    this.successMessage = '';
    this.errorMessage = '';

    if (this.locationExists(this.locationName)) {
      this.errorMessage = 'This location already exists.';
      return;
    }

    const newLocation = {
      locationId: 0, // always 0 as it's handled by backend
      locationName: this.locationName,
      code: this.code
    };

    this.http.post('https://api.freeprojectapi.com/api/BusBooking/PostBusLocation', newLocation)
      .subscribe({
        next: () => {
          this.successMessage = 'Location added successfully!';
          this.locationName = '';
          this.code = '';
          this.fetchLocations();
        },
        error: () => {
          this.errorMessage = 'Error adding location. Please try again.';
        }
      });
  }
}
