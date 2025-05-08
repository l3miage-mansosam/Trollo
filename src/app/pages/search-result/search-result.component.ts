import { Component, inject } from '@angular/core';
import { ActivatedRoute, Router} from '@angular/router';
import { ISearchBus, Search } from '../../model/model';
import { SearchService } from '../../service/search.service';
import { DatePipe } from '@angular/common';



@Component({
  selector: 'app-search-result',
  imports: [DatePipe],
  templateUrl: './search-result.component.html',
  styleUrl: './search-result.component.css'
})
export class SearchResultComponent {
  activatedRoute = inject(ActivatedRoute);
    searchObj: Search = new Search();
    searchService = inject(SearchService);
    serachData:ISearchBus[] = [];
    router = inject(Router);
  constructor() {
    this.activatedRoute.params.subscribe((params) => {
      this.searchObj.fromLocationId= params['fromId'];
      this.searchObj.toLocationId= params['toId'];
      this.searchObj.date= params['date'];
      console.log(this.searchObj);
      this.getSearchResult();
    }
    )}
    getSearchResult() {
      this.searchService.searchBus(this.searchObj.fromLocationId, this.searchObj.toLocationId, this.searchObj.date).subscribe((data: any) => {
        console.log(data);
        this.serachData = data;
      }
      );
    }
    navigateToBooking(thisScheduleId: number) {
      this.router.navigate(['/book-ticket', thisScheduleId]);
    }
}
  

