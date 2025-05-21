import { Component,inject} from '@angular/core';
import { ActivatedRoute } from '@angular/router';
import { SearchService } from '../../service/search.service';
import { IBusScheduleDetails ,IPassenger,IBusBooking} from '../../model/model';
import { DatePipe } from '@angular/common';
import { CommonModule } from '@angular/common';
import { NgClass } from '@angular/common';
// import ngModel
import { FormsModule } from '@angular/forms';
import { User } from '../../model/model';

@Component({
  selector: 'app-book-ticket',
  imports: [NgClass,CommonModule,FormsModule],
  templateUrl: './book-ticket.component.html',
  styleUrl: './book-ticket.component.css'
})
export class BookTicketComponent {
  busDetails!: IBusScheduleDetails;
  searchService = inject(SearchService);
  activatedRoute = inject(ActivatedRoute);
  seatNumberList: number[] = [];
  selectedSeatsArray: IPassenger[] = [];
  bookedSeats: number[] = [];
  bookTicket:IBusBooking = new IBusBooking();
    user: User = new User();

  constructor() {

    this.activatedRoute.params.subscribe((res:any) => {
      const { scheduleId } = res;
      this.getBusDetails(scheduleId);
      this.bookTicket.scheduleId = scheduleId;
      this.bookTicket.bookingDate= new Date();
      const storedUser = localStorage.getItem('user');
      console.log("User from local storage now: ", storedUser);
      if (storedUser) {
        this.user = JSON.parse(storedUser);
        this.bookTicket.custId = this.user.userId;
      }    
      this.bookTicket.bookingDate = new Date();
    }
    )    
    this.activatedRoute.params.subscribe((res:any) => {
      const { scheduleId } = res;
      this.BookedSeats(scheduleId);
    }
    ) 

}
getBusDetails(scheduleId: number) {
  console.log(scheduleId);
  this.searchService.getBusScheduleById(scheduleId).subscribe((data:IBusScheduleDetails) => {
    console.log(data);
    this.busDetails = data;
    for (let i = 1; i <= this.busDetails.totalSeats; i++) {
      this.seatNumberList.push(i);
    }
  });
}
onSelect(seatNo: number) {

  const isExist = this.selectedSeatsArray.findIndex((passenger) => passenger.seatNo === seatNo);
  if (isExist!== -1) {
    this.selectedSeatsArray.splice(isExist, 1);
  }else{
  const newPassenger: IPassenger = {
    passengerId: 0,
    bookingId: 0,
    passengerName: "",
    age: 0,
    seatNo: seatNo,
    gender: "",

  
};
this.selectedSeatsArray.push(newPassenger)

}
}
checkUSerLoggedIn() {
  const user = localStorage.getItem('user');
  if (user) {
    return true;
  }
  return false;
}

BookedSeats(scheduleId: number) {
  this.searchService.getBookedSeats(scheduleId).subscribe((data: number[]) => {
    this.bookedSeats = data;
  });
}


isSelected(seatNo: number): boolean {
  return this.selectedSeatsArray.some((passenger) => passenger.seatNo === seatNo);
}
isBooked(seatNo: number): boolean { 
 
return this.bookedSeats.includes(seatNo);}

bookTicketBooking(){
  this.bookTicket.busBookingPassengers=this.selectedSeatsArray;

  this.searchService.createNewBooking(this.bookTicket).subscribe((Res:any)=>{
    alert("ticket booked success")

  })
}
openLoginModal() {
//avant de naviger vers la page de connexion, enregistrez l'URL actuelle dans le stockage local et
//  aussi stocker les données du formulaire 
const currentUrl = window.location.pathname;
const redirectUrl = encodeURIComponent(currentUrl);
  localStorage.setItem('redirectUrl', redirectUrl);
window.location.href = '/login';
}
goBack() {
  window.history.back();
}

}