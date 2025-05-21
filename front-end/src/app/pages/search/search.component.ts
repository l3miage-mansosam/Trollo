import { Component, inject,OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Search } from '../../model/model';
import { FormsModule } from '@angular/forms';
import { Router } from '@angular/router';
import { CommonModule } from '@angular/common';
@Component({
  selector: 'app-search',
  imports: [FormsModule, CommonModule],
  templateUrl: './search.component.html',
  styleUrl: './search.component.css'
})
export class SearchComponent implements OnInit {
  http= inject(HttpClient);
  locationList: any = [];
  router = inject(Router);

  searchObj: Search = new Search();
  

  ngOnInit() {
    this.getAllLocations();
  }
  getAllLocations() {
    
    this.http.get('https://api.freeprojectapi.com/api/BusBooking/GetBusLocations').subscribe((data: any) => {
      console.log(data);
      this.locationList = data;
    }
    );
  }
  searchBus() {


    this.router.navigate(['/search-result', this.searchObj.fromLocationId, this.searchObj.toLocationId, this.searchObj.date]);

    }
   
  
  

}
