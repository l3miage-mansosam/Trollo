import { Component, inject } from '@angular/core';
import { ActivatedRoute, Router} from '@angular/router';
import { ISearchBus, Search } from '../../model/model';
import { SearchService } from '../../service/search.service';
import { DatePipe } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { OnInit } from '@angular/core';

@Component({
    selector: 'app-search-result',
    imports: [DatePipe, FormsModule, CommonModule],
    templateUrl: './search-result.component.html',
    styleUrl: './search-result.component.css'
})
export class SearchResultComponent implements OnInit {
    activatedRoute = inject(ActivatedRoute);
    searchObj: Search = new Search();
    searchService = inject(SearchService);
    serachData:ISearchBus[] = [];
    router = inject(Router);


    filter = {
        price: '',
        departure: '',
        vendor: '',
        minSeats: 0
    };

    filteredResults :ISearchBus[]=[];

    constructor() {
        this.activatedRoute.params.subscribe((params) => {
                this.searchObj.fromLocationId= params['fromId'];
                this.searchObj.toLocationId= params['toId'];
                this.searchObj.date= params['date'];
                console.log(this.searchObj);
                this.getSearchResult();

            }
        )}


    ngOnInit() {
        this.filteredResults = [...this.serachData];
    }



    ngDoCheck() {
        this.applyFilters();
    }
    getSearchResult() {
        this.searchService.searchBus(this.searchObj.fromLocationId, this.searchObj.toLocationId, this.searchObj.date).subscribe((data: any) => {
                console.log(data);
                this.serachData = data;
                this.applyFilters();
            }
        );
    }
    navigateToBooking(thisScheduleId: number) {
        this.router.navigate(['/book-ticket', thisScheduleId]);
    }
    getDuration(departureTime: Date | string, arrivalTime: Date | string): string {
        const dep = new Date(departureTime).getTime();
        const arr = new Date(arrivalTime).getTime();

        // différence en millisecondes
        let diffMs = arr - dep;
        // prendre en compte passage minuit
        if (diffMs < 0) {
            diffMs += 24 * 60 * 60 * 1000;
        }

        // convertit en minutes totales
        const totalMinutes = Math.floor(diffMs / (1000 * 60));

        // calcule jours, heures et minutes
        const minsPerDay = 24 * 60;
        const days = Math.floor(totalMinutes / minsPerDay);
        const remainderMinutes = totalMinutes % minsPerDay;
        const hours = Math.floor(remainderMinutes / 60);
        const minutes = remainderMinutes % 60;

        // construction de la chaîne
        const parts: string[] = [];
        if (days > 0) {
            parts.push(`${days}jr`);
        }
        if (hours > 0 || days > 0) {
            parts.push(`${hours}h`);
        }
        parts.push(`${minutes}m`);

        return parts.join(' ');
    }

    applyFilters() {
        this.filteredResults = [...this.serachData];

        if (this.filter.price === 'asc') {
            this.filteredResults.sort((a, b) => a.price - b.price);
        } else if (this.filter.price === 'desc') {
            this.filteredResults.sort((a, b) => b.price - a.price);
        }

        if (this.filter.departure === 'asc') {
            this.filteredResults.sort((a, b) => new Date(a.departureTime).getTime() - new Date(b.departureTime).getTime());
        } else if (this.filter.departure === 'desc') {
            this.filteredResults.sort((a, b) => new Date(b.departureTime).getTime() - new Date(a.departureTime).getTime());
        }

        if (this.filter.vendor) {
            this.filteredResults = this.filteredResults.filter(bus => bus.vendorName === this.filter.vendor);
        }

        if (this.filter.minSeats > 0) {
            this.filteredResults = this.filteredResults.filter(bus => bus.availableSeats >= this.filter.minSeats);
        }
    }
}
