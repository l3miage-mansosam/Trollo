import { Component,inject } from '@angular/core';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { BusSchedule ,Search,ISearchBus} from '../../model/model';
import { SearchService } from '../../service/search.service';
import { Router } from '@angular/router';
import { HttpClient } from '@angular/common/http';
import { OnInit } from '@angular/core';
import { ViewChild, ElementRef } from '@angular/core';


@Component({
  selector: 'app-schedule',
  imports: [CommonModule, FormsModule],
  templateUrl: './schedule.component.html',
  styleUrl: './schedule.component.css'
})
export class ScheduleComponent implements OnInit {
@ViewChild('openEditModalBtn') openEditModalBtn!: ElementRef;
@ViewChild('openDeleteModalBtn') openDeleteModalBtn!: ElementRef;
  http= inject(HttpClient);
  locationList: any = [];
  router = inject(Router);
   showPostForm = true
   vendorBuses: ISearchBus[] = [];
availableSeatsMap: { [key: number]: number | undefined } = {};

busSchedule: BusSchedule = new BusSchedule();
busScheduleEdit: BusSchedule = new BusSchedule();
 searchObj: Search = new Search();
 selectedScheduleId: number = 0;


 
 


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
    vendorId: 424,
    busName: '',
    busVehicleNo: '',
    fromLocation: 0,
    toLocation: 0,
    departureTime:"",
    arrivalTime: "",
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
    departureTime: "",
    arrivalTime: "",
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
  toggleView() {
    this.showPostForm = !this.showPostForm;
      this.getVendorBuses();
  }
 getVendorBuses() {
  this.searchService.getSchedulesByVendorId(this.busSchedule.vendorId).subscribe({
    next: (schedules) => {
      this.vendorBuses = schedules;

      // Précharger les sièges disponibles
      schedules.forEach((bus) => {
        this.searchService.getBookedSeats(bus.scheduleId).subscribe((booked: number[]) => {
          this.availableSeatsMap[bus.scheduleId] = bus.totalSeats - booked.length;
        });
      });
    },
    error: (err) => console.error('Error fetching vendor buses:', err)
  });
}

editSchedule(scheduleId: number): void {
  this.searchService.getBusScheduleById2(scheduleId).subscribe({
    next: (schedule) => {
      this.busScheduleEdit = schedule;
      // Simule un clic sur le bouton pour ouvrir le modal
      console.log('Schedule to edit:', this.busScheduleEdit);
      this.openEditModalBtn.nativeElement.click();
      console.log('Edit modal opened');
    },
    error: (err) => {
      console.error('Failed to load schedule', err);
    }
  });
}

deleteSchedule(scheduleId: number): void {
  this.selectedScheduleId = scheduleId;
  this.openDeleteModalBtn.nativeElement.click(); // Ouvre le modal via le bouton caché
}


  getScheduleById(vendorId: number) {
    this.searchService.getBusScheduleById2(vendorId).subscribe({
      next: (response) => {
        console.log('Schedule details:', response);
        this.busScheduleEdit = response;
      },
      error: (error) => {
        console.error('Error fetching schedule details:', error);
      }
    });
  }
  updateSchedule() {
  
   this.busScheduleEdit.scheduleId = 0;
    console.log('Updating schedule with ID:', this.busScheduleEdit.scheduleId);
    this.searchService.createBusSchedule(this.busScheduleEdit).subscribe({
      next: (response) => {
        console.log('Schedule updated successfully:', response);
        this.getVendorBuses(); 
      },
      error: (error) => {
        console.error('Error updating schedule:', error);
      }
    });
  }
confirmDelete(): void {
  this.searchService.deleteSchedule(this.selectedScheduleId).subscribe({
    next: () => {
      this.getVendorBuses(); // Recharge les données
    },
    error: (err) => {
      console.error('Failed to delete schedule', err);
    }
  });
}


}