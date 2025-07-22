import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';

@Injectable({
  providedIn: 'root'
})
export class ChatbotService {
  private apiUrl = '/chatbot'; // proxy.conf.json，转到 http://localhost:5000/chatbot

  constructor(private http: HttpClient) {}

  send(message: string): Observable<string> {
    return this.http
      .post<{ reply: string }>(this.apiUrl, { message })
      .pipe(map(res => res.reply)); // 获取 reply 字符串
  }

  getRoutes(): Observable<any[]> {
    return this.http.get<any[]>('assets/bus-routes.json');
  }
}
