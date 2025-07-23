import {Injectable} from '@angular/core';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {Observable} from 'rxjs';
import {
  ApiResponse,
  ApiResponseLogin, Booking,
  BusSchedule,
  IBusBooking,
  IBusScheduleDetails,
  ISearchBus,
  User
} from '../model/model';

@Injectable({
  providedIn: 'root'
})
export class SearchService {
  private apiUrl = 'https://api.freeprojectapi.com/api/BusBooking';
  private apiUrlSymfony = 'http://localhost:8000/api';


  constructor(private http: HttpClient) {
  }

  searchBus(fromLocationId: string, toLocationId: string, date: string) {
    return this.http.get(`https://api.freeprojectapi.com/api/BusBooking/searchBus2?fromLocation=${fromLocationId}&toLocation=${toLocationId}&travelDate=${date}`);

  }

  getBusScheduleById(vendorId: number): Observable<IBusScheduleDetails> {
    console.log("scheduleId", vendorId);
    return this.http.get<IBusScheduleDetails>(`https://api.freeprojectapi.com/api/BusBooking/GetBusScheduleById?id=${vendorId}`);
  }

  postNewUser(userObj: any) {
    return this.http.post<any>('https://api.freeprojectapi.com/api/BusBooking/AddNewUser', userObj);

  }

  registerUser(userObj: object): Observable<ApiResponse<User>> {
    return this.http.post<ApiResponse<User>>(`${this.apiUrlSymfony}/register`, userObj);
  }

  loginUser(credentials: { email: string, password: string }): Observable<ApiResponseLogin<any>> {
    console.log('credentials', credentials);
    return this.http.post<ApiResponseLogin<any>>(`${this.apiUrlSymfony}/login`, credentials);
  }

  getUserByToken(token: string): Observable<User> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
    return this.http.get<User>(`${this.apiUrlSymfony}/me`, {headers});
  }

  createNewBooking(obj: IBusBooking) {
    return this.http.post(`${this.apiUrl}/PostBusBooking`, obj)
  }

  getBookedSeats(scheduleId: number): Observable<number[]> {
    return this.http.get<number[]>(`${this.apiUrl}/getBookedSeats?shceduleId=${scheduleId}`);
  }

  createBusSchedule(obj: {
    busId: string;
    roadId: string;
    estimated_time: string;
    departure_date: string;
    unit_price: number;
    start_city_id: string;
    arrived_city_id: string
  }, token: string) {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
    return this.http.post<ApiResponse<any>>(`${this.apiUrlSymfony}/sessions`, obj, {headers})
  }

  updateBusSchedule(obj: {
    busId: string;
    roadId: string;
    estimated_time: string;
    departure_date: string;
    unit_price: number;
    start_city_id: string;
    arrived_city_id: string
  }, sessionId: string, token: string): Observable<ApiResponse<any>> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
    return this.http.put<ApiResponse<any>>(`${this.apiUrlSymfony}/sessions/${sessionId}`, obj, {headers});
  }

  registerVendor(userObj: User): Observable<ApiResponse<null>> {

    return this.http.post<ApiResponse<null>>(`${this.apiUrl}/register`, userObj);
  }

  postBusVendor(obj: any) {
    return this.http.post(`${this.apiUrl}/PostBusVendor`, obj)
  }

  getSchedulesByVendorId(vendorId: number, token: string): Observable<ISearchBus[]> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
    return this.http.get<ISearchBus[]>(`${this.apiUrlSymfony}/sessions`, {headers});
  }

  deleteSchedule(scheduleId: string, token: string): Observable<ApiResponse<null>> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
    return this.http.delete<ApiResponse<null>>(`${this.apiUrlSymfony}/sessions/${scheduleId}`, {headers});
  }

  getBusScheduleById2(scheduleId: string, token: string): Observable<ISearchBus> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
    return this.http.get<ISearchBus>(`${this.apiUrlSymfony}/sessions?id=${scheduleId}`, {headers});
  }

  getBookings(token:string): Observable<ApiResponse<Booking[]>> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });

    return this.http.get<ApiResponse<Booking[]>>(`${this.apiUrlSymfony}/bookings`, {headers});
  }

  createBooking(obj: {
    user_id: any;
    session_id: string;
    reservation_date: string;
    price: number
  }, token: string): Observable<ApiResponse<Booking>> {
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });
    return this.http.post<ApiResponse<Booking>>(`${this.apiUrlSymfony}/bookings`, obj, {headers});
  }
}
