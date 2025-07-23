import { Component, ViewChild, ElementRef } from '@angular/core';
import { SearchService } from '../../service/search.service';
import { DatePipe, NgForOf, NgIf } from '@angular/common';
import { Booking } from '../../model/model';
import {FormsModule} from '@angular/forms';
import {ScheduleComponent} from '../schedule/schedule.component';

@Component({
  selector: 'app-bookings',
  imports: [DatePipe, NgIf, NgForOf, FormsModule, ScheduleComponent],
  templateUrl: './bookings.component.html',
  styleUrl: './bookings.component.css',
})
export class BookingsComponent {
  bookings: Booking[] = [];
  isLoading: boolean = false;
  selectedSessionId: string = '';
  selectedSession: any = null; // Ajout pour stocker les informations sur la session
  reservationDate: string = ''; // Stocke la date d'une réservation

  @ViewChild('openBookingModalBtn') openBookingModalBtn!: ElementRef;

  constructor(private searchService: SearchService) {}

  ngOnInit(): void {
    this.fetchBookings();
  }

  fetchBookings(): void {
    this.isLoading = true;
    const token = localStorage.getItem('userToken');
    if (!token) {
      return;
    }
    this.searchService.getBookings(token).subscribe({
      next: (booking) => {
        this.bookings = booking.data;
        this.isLoading = false;
      },
      error: (err) => {
        console.error('Erreur de récupération :', err);
        this.isLoading = false;
      },
    });
  }

  openBookingModal(sessionId: string): void {
    this.selectedSessionId = sessionId;
    const token = localStorage.getItem('userToken');
    if (!token) {
      // this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }

    // Récupérer les informations sur la session (via un service ou un tableau local)
    this.searchService.getBusScheduleById2(sessionId, token).subscribe({
      next: (session) => {
        this.selectedSession = session; // Charger les détails de la session dans le modal
        this.openBookingModalBtn.nativeElement.click(); // Ouvrir le modal
      },
      error: (err) => {
        console.error("Erreur lors de la récupération de la session :", err);
      },
    });
  }

  confirmBooking(): void {
    if (!this.selectedSessionId || !this.reservationDate) return;

    // Appel au service pour confirmer la réservation
/*    this.searchService.bookSession(this.selectedSessionId, this.reservationDate).subscribe({
      next: () => {
        console.log('Réservation confirmée avec succès');
        this.fetchBookings(); // Actualise la liste des réservations
      },
      error: (err) => {
        console.error("Erreur lors de la confirmation de la réservation :", err);
      },
    });*/
  }
}
