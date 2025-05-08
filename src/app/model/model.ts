
export class Search {
    fromLocationId: string;
    toLocationId: string;
    date: string;
    constructor( ){
        this.fromLocationId = "";
        this.toLocationId = "";
        this.date = "";
    }
}

export  interface PassengerInfo {
  seatNo: number;
  name: string;
  age: number;
  gender: string;
}
export interface ISearchBus {
    availableSeats: number;
    totalSeats: number;
    price: number;
    arrivalTime: string;     
    scheduleId: number;
    departureTime: string;    
    busName: string;
    busVehicleNo: string;
    fromLocationName: string;
    toLocationName: string;
    vendorName: string;
    scheduleDate: string;     
    vendorId: number;
  }
  export interface IBusScheduleDetails {
    scheduleId: number;
    vendorId: number;
    busName: string;
    busVehicleNo: string;
    fromLocation: number;
    toLocation: number;
    departureTime: string; 
    arrivalTime: string;   
    scheduleDate: string;  
    price: number;
    totalSeats: number;
  }
  export class IBusBooking {
    bookingId: number;
    custId: number;
    bookingDate: Date; // ISO string format (e.g. "2025-04-29T23:52:53.930Z")
    scheduleId: number;
    busBookingPassengers: IPassenger[];
    constructor() {
      this.bookingId = 0;
      this.custId = 0;
      this.bookingDate = new Date();
      this.scheduleId = 0;
      this.busBookingPassengers = [];
    }
  }
  
  export class IPassenger {
    passengerId: number;
    bookingId: number;
    passengerName: string;
    age: number;
    gender: string;
    seatNo: number;
    constructor() {
      this.passengerId = 0;
      this.bookingId = 0;
      this.passengerName = "";
      this.age =0;
        this.gender = "";   
        this.seatNo = 0;
  }

}
export interface ApiResponse<T> {
  message: string;
  result: boolean;
  data: T;
}

export interface User {
  userId: number;
  userName: string;
  emailId: string;
  fullName: string;
  role: string;
  createdDate: string;
  password: string; // Optionnel car ne doit pas être stocké
  projectName: string;
  refreshToken: string;
  refreshTokenExpiryTime: string;
}
export class User {
  userId: number;
  userName: string;
  emailId: string;
  fullName: string;
  role: string;
  createdDate: string; // ISO string format
  password: string;
  projectName: string;
  refreshToken: string;
  refreshTokenExpiryTime: string; // ISO string format
  constructor() {
    this.userId = 0;
    this.userName = "";
    this.emailId = "";
    this.fullName = "";
    this.role = "";
    this.createdDate = "";
    this.password = "";
    this.projectName = "";
    this.refreshToken = "";
    this.refreshTokenExpiryTime = "";
  }
}
