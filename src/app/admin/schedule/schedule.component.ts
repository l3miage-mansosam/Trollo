import { Component,inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { BusSchedule ,Search} from '../../model/model';
import { SearchService } from '../../service/search.service';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { OnInit } from '@angular/core';
@Component({
  selector: 'app-schedule',
  imports: [CommonModule, FormsModule],
  templateUrl: './schedule.component.html',
  styleUrl: './schedule.component.css'
})
export class ScheduleComponent implements OnInit {
  http= inject(HttpClient);
  locationList: any = [];
  router = inject(Router);
busSchedule: BusSchedule = new BusSchedule();
 searchObj: Search = new Search();
constructor(private searchService: SearchService) {
  const localStorageData = localStorage.getItem('user');
  if (localStorageData) {
    const user = JSON.parse(localStorageData);
    this.busSchedule.vendorId = user.userId;
    console.log('User ID from local storage marwa:', this.busSchedule.vendorId);
  } else {
    console.error('User not found in local storage');
  }

  this.busSchedule = {
    scheduleId: 0,
    vendorId: 118,
    busName: '',
    busVehicleNo: '',
    fromLocation: 0,
    toLocation: 0,
    departureTime: new Date(),
    arrivalTime: new Date(),
    scheduleDate: new Date(),
    price: 0,
    totalSeats: 0
  };
}

  errorMessage: string = '';

  submitSchedule() {
    
    console.log("busSchedule",this.busSchedule);

    // Soumettre les données du bus à l'API
    this.searchService.createBusSchedule(this.busSchedule).subscribe({
      next: (response) => {
        console.log('Bus schedule created successfully:', response);
        this.resetForm(); // Réinitialisez le formulaire après une soumission réussie
      },
      error: () => {
        this.errorMessage = 'An error occurred while creating the bus schedule.';
      }
    });
  
    console.log('Form submitted successfully', this.busSchedule);
  }
  
   

validateSchedule(schedule: any): boolean {
  return schedule.busName.trim() !== '' && // Vérifie que busName n'est pas vide
         schedule.busVehicleNo.trim() !== '' && // Vérifie que busVehicleNo n'est pas vide
         schedule.fromLocation > 0 && // Vérifie que fromLocation est un nombre supérieur à 0
         schedule.toLocation > 0 && // Vérifie que toLocation est un nombre supérieur à 0
         schedule.departureTime instanceof Date && !isNaN(schedule.departureTime.getTime()) && // Vérifie que departureTime est une date valide
         schedule.arrivalTime instanceof Date && !isNaN(schedule.arrivalTime.getTime()) && // Vérifie que arrivalTime est une date valide
         schedule.scheduleDate instanceof Date && !isNaN(schedule.scheduleDate.getTime()) && // Vérifie que scheduleDate est une date valide
         schedule.price > 0 && // Vérifie que price est un nombre supérieur à 0
         schedule.totalSeats > 0; // Vérifie que totalSeats est un nombre supérieur à 0
}


resetForm() { 
  this.busSchedule = {
    scheduleId: 0,
    vendorId: 0,
    busName: '',
    busVehicleNo: '',
    fromLocation: 0,
    toLocation: 0,
    departureTime: new Date(),
    arrivalTime: new Date(),
    scheduleDate: new Date(),
    price: 0,
    totalSeats: 0
  };
  this.errorMessage = '';
}
ngOnInit() {
  this.getAllLocations();
}
getAllLocations() {
  
  this.http.get('https://api.freeprojectapi.com/api/BusBooking/GetBusLocations').subscribe((data: any) => {
    console.log(data);
    this.locationList = data;
  }
  );
}

}