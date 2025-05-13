import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { IBusScheduleDetails,User,ApiResponse, IBusBooking,BusSchedule} from '../model/model';

@Injectable({
  providedIn: 'root'
})
export class SearchService {
  private apiUrl = 'https://api.freeprojectapi.com/api/BusBooking';
  //https://projectapi.gerasim.in/api/BusBooking/

  constructor(private http: HttpClient) { }

  searchBus(fromLocationId: string, toLocationId: string, date: string) {
    return this.http.get(`https://api.freeprojectapi.com/api/BusBooking/searchBus2?fromLocation=${fromLocationId}&toLocation=${toLocationId}&travelDate=${date}`);

  }
  getBusScheduleById(scheduleId: number): Observable<IBusScheduleDetails> {
    console.log("scheduleId", scheduleId);
    return this.http.get<IBusScheduleDetails>(`https://api.freeprojectapi.com/api/BusBooking/GetBusScheduleById?id=${scheduleId}`);
  }
  postNewUser(userObj:any){
    return this.http.post<any>('https://api.freeprojectapi.com/api/BusBooking/AddNewUser', userObj);
    
  }

  registerUser(userObj: User): Observable<ApiResponse<null>> {
    return this.http.post<ApiResponse<null>>(`${this.apiUrl}/AddNewUser`, userObj);
  }

  loginUser(credentials: { userName: string, password: string }): Observable<ApiResponse<User>> {
    return this.http.post<ApiResponse<User>>(`${this.apiUrl}/login`, credentials);
  }
  createNewBooking(obj:IBusBooking){
    return this.http.post(`${this.apiUrl}/PostBusBooking`,obj)
  }
  getBookedSeats(scheduleId: number): Observable<number[]> {
    return this.http.get<number[]>(`${this.apiUrl}/getBookedSeats?shceduleId=${scheduleId}`);
  }
  createBusSchedule(obj:BusSchedule){
    return this.http.post(`${this.apiUrl}/PostBusSchedule`,obj)
  }
}

