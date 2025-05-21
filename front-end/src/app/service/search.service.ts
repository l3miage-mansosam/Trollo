import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, scheduled } from 'rxjs';
import { IBusScheduleDetails,User,ApiResponse, IBusBooking,BusSchedule, Vendor,ISearchBus} from '../model/model';

@Injectable({
  providedIn: 'root'
})
export class SearchService {
  private apiUrl = 'https://api.freeprojectapi.com/api/BusBooking';


  constructor(private http: HttpClient) { }

  searchBus(fromLocationId: string, toLocationId: string, date: string) {
    return this.http.get(`https://api.freeprojectapi.com/api/BusBooking/searchBus2?fromLocation=${fromLocationId}&toLocation=${toLocationId}&travelDate=${date}`);

  }
  getBusScheduleById(vendorId: number): Observable<IBusScheduleDetails> {
    console.log("scheduleId", vendorId);
    return this.http.get<IBusScheduleDetails>(`https://api.freeprojectapi.com/api/BusBooking/GetBusScheduleById?id=${vendorId}`);
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
  updateBusSchedule(obj: BusSchedule): Observable<ApiResponse<null>> {
  return this.http.put<ApiResponse<null>>(`${this.apiUrl}/PutBusSchedule`, obj);
}
  registerVendor(userObj: User): Observable<ApiResponse<null>> {
    
    return this.http.post<ApiResponse<null>>(`${this.apiUrl}/PostBusVendor`, userObj);
  }

  postBusVendor(obj:any){
    return this.http.post(`${this.apiUrl}/PostBusVendor`,obj)
  }
 
getSchedulesByVendorId(vendorId: number): Observable<ISearchBus[]> {
  return this.http.get<ISearchBus[]>(`${this.apiUrl}/GetBusSchedules?vendorId=${vendorId}`);
}

deleteSchedule(scheduleId: number): Observable<ApiResponse<null>> {
  return this.http.delete<ApiResponse<null>>(`${this.apiUrl}/DeleteBusSchedule?id=${scheduleId}`);
}
getBusScheduleById2(scheduleId: number): Observable<BusSchedule> {
  return this.http.get<BusSchedule>(`${this.apiUrl}/GetBusScheduleById?id=${scheduleId}`);
}

}
