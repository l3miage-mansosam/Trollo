/* src/app/pages/search/search.component.ts */

import { Component, OnInit, inject } from '@angular/core';
import { CommonModule }   from '@angular/common';
import { FormsModule }    from '@angular/forms';
import { HttpClient }     from '@angular/common/http';
import { Router }         from '@angular/router';

import { ChatbotComponent } from '../../components/chatbot/chatbot.component';   // 🆕 路径别写错
import { Search }           from '../../model/model';

@Component({
  selector: 'app-search',
  standalone: true,                               // 🆕 让它成为 stand-alone
  templateUrl: './search.component.html',
  styleUrls:  ['./search.component.css'],
  imports:    [CommonModule, FormsModule, ChatbotComponent]  // 🆕 把 ChatbotComponent 引进来
})
export class SearchComponent implements OnInit {

  private http   = inject(HttpClient);
  private router = inject(Router);

  locationList: any[] = [];
  searchObj:    Search = new Search();

  ngOnInit(): void {
    this.getAllLocations();
  }

  /** 读取全部车站 */
  getAllLocations(): void {
    this.http.get<any>('https://api.freeprojectapi.com/api/BusBooking/GetBusLocations')
             .subscribe(data => this.locationList = data);
  }

  /** 点击搜索按钮 */
  searchBus(): void {
    const { fromLocationId, toLocationId, date } = this.searchObj;
    this.router.navigate(['/search-result', fromLocationId, toLocationId, date]);
  }
}
