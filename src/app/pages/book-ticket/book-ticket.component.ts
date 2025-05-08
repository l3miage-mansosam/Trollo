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
  bookTicket:IBusBooking = new IBusBooking();
    user: User = new User();

  constructor() {
    this.activatedRoute.params.subscribe((res:any) => {
      const scheduleId = res.scheduleId;
      this.getBusDetails(scheduleId);
      this.bookTicket.scheduleId = scheduleId;
      this.bookTicket.bookingDate= new Date();
      const storedUser = localStorage.getItem('user');
      console.log("User from local storage2: ", storedUser);
      if (storedUser) {
        this.user = JSON.parse(storedUser);
      
        this.bookTicket.custId = this.user.userId;
      }    
      this.bookTicket.bookingDate = new Date();
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




isSelected(seatNo: number): boolean {
  return this.selectedSeatsArray.some((passenger) => passenger.seatNo === seatNo);
}
isBooked(seatNo: number): boolean { 
  return this.busDetails.totalSeats < seatNo;
}

bookTicketBooking(){
  this.bookTicket.busBookingPassengers=this.selectedSeatsArray;

  this.searchService.createNewBooking(this.bookTicket).subscribe((Res:any)=>{
    alert("ticket booked success")

  })

}






}
