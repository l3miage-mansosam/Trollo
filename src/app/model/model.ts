
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
  createdDate: Date;
  password: string; // Optionnel car ne doit pas être stocké
  projectName: string;
  refreshToken: string;
  refreshTokenExpiryTime: Date;
}
export class User {
  userId: number;
  userName: string;
  emailId: string;
  fullName: string;
  role: string;
  createdDate: Date; // ISO string format
  password: string;
  projectName: string;
  refreshToken: string;
  refreshTokenExpiryTime: Date; // ISO string format
  constructor() {
    this.userId = 0;
    this.userName = "";
    this.emailId = "";
    this.fullName = "";
    this.role = "";
    this.createdDate = new Date();
    this.password = "";
    this.projectName = "BusBooking";
    this.refreshToken = "";
    this.refreshTokenExpiryTime = new Date();
  }

    
}
export class BusSchedule {
  scheduleId: number;
  vendorId: number;
  busName: string;
  busVehicleNo: string;
  fromLocation: number;
  toLocation: number;
  departureTime: Date;  // ISO Date String
  arrivalTime: Date;    // ISO Date String
  scheduleDate: Date;   // ISO Date String
  price: number;
  totalSeats: number;

  constructor() {
    this.scheduleId = 0;
    this.vendorId = 1378;
    this.busName = "";
    this.busVehicleNo = "";
    this.fromLocation = 0;
    this.toLocation = 0;
    this.departureTime = new Date(),
    this.arrivalTime= new Date(),
    this.scheduleDate = new Date(),
    this.price = 0;
    this.totalSeats = 0;
  }

}

