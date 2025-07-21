import {Component, inject} from '@angular/core';
import {FormsModule} from '@angular/forms';
import {CommonModule} from '@angular/common';
import {BusSchedule, Search, ISearchBus, ApiResponse, Bus, Road} from '../../model/model';
import {SearchService} from '../../service/search.service';
import {Router} from '@angular/router';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {OnInit} from '@angular/core';
import {ViewChild, ElementRef} from '@angular/core';


@Component({
  selector: 'app-schedule',
  imports: [CommonModule, FormsModule],
  templateUrl: './schedule.component.html',
  styleUrl: './schedule.component.css'
})
export class ScheduleComponent implements OnInit {
  @ViewChild('openEditModalBtn') openEditModalBtn!: ElementRef;
  @ViewChild('openDeleteModalBtn') openDeleteModalBtn!: ElementRef;
  http = inject(HttpClient);
  roadList: Road[] = [];
  busList: Bus[] = [];
  router = inject(Router);
  showPostForm = true
  vendorBuses: ISearchBus[] = [];
  availableSeatsMap: { [key: number]: number | undefined } = {};
  arrivalTimeOurs: number = 0;
  arrivalTimeMinutes: number = 0;

  busSchedule: BusSchedule = new BusSchedule();
  busScheduleEdit: BusSchedule = new BusSchedule();
  searchObj: Search = new Search();
  selectedScheduleId: number = 0;

  selectedBus: any = new Bus();
  selectedRoad: any = new Road();

  constructor(private searchService: SearchService) {
    const localStorageData = localStorage.getItem('user');

    if (localStorageData) {
      const user = JSON.parse(localStorageData);
      this.busSchedule.vendorId = user.userId;
      console.log('User ID from local storage marwa:', this.busSchedule.vendorId);
    } else {
      console.error('User not found in local storage');
    }

    this.busSchedule = {
      scheduleId: 0,
      vendorId: 424,
      busId: '',
      busName: '',
      busVehicleNo: '',
      start_city_id: '',
      arrived_city_id: '',
      // departureTime: "",
      roadId: "",
      arrivalTime: "",
      scheduleDate: new Date(),
      price: 0,
      totalSeats: 0
    };

  }

  errorMessage: string = '';

  submitSchedule(dataForm: {
    busId: string;
    roadId: string;
    estimated_time: string;
    departure_date: Date;
    unit_price: number;
    start_city_id: string;
    arrived_city_id: string
  }) {

    console.log("busSchedule",dataForm);
    const token = localStorage.getItem('userToken');
    if (!token) {
      this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }

    // Soumettre les données du bus à l'API
    this.searchService.createBusSchedule(dataForm, token).subscribe({
      next: (response) => {
        console.log('Bus schedule created successfully:', response);
        this.resetForm(); // Réinitialisez le formulaire après une soumission réussie
      },
      error: () => {
        this.errorMessage = 'An error occurred while creating the bus schedule.';

      }
    });

    // console.log('Form submitted successfully', this.busSchedule);
  }


  validateSchedule(): void {
    // Formatage de l'heure
    const formattedTime = this.getFormattedTime();

    // Vérification des champs obligatoires
    const isValid =
      this.busSchedule.busId?.trim() !== '' &&
      this.busSchedule.roadId?.trim() !== '' &&
      formattedTime &&
      this.busSchedule.scheduleDate &&
      this.busSchedule.price && this.busSchedule.price > 0;

    if (isValid) {
      // On ne garde que les champs valides
      const cleanedSchedule = {
        busId: this.busSchedule.busId.trim(),
        roadId: this.busSchedule.roadId.trim(),
        estimated_time: formattedTime,
        departure_date: this.busSchedule.scheduleDate,
        unit_price: this.busSchedule.price,
        start_city_id: this.busSchedule.start_city_id,
        arrived_city_id: this.busSchedule.arrived_city_id,
      };

      this.submitSchedule(cleanedSchedule); // ← On passe un objet propre
    } else {
      console.warn('Certains champs sont invalides. Vérifiez votre saisie.');
    }
  }


  getFormattedTime(): string {
    const hours = this.arrivalTimeOurs ?? 0;
    const minutes = this.arrivalTimeMinutes ?? 0;

    const paddedHours = hours.toString().padStart(2, '0');
    const paddedMinutes = minutes.toString().padStart(2, '0');

    return `${paddedHours}:${paddedMinutes}`;
  }

  resetForm() {
    this.busSchedule = {
      scheduleId: 0,
      vendorId: 0,
      busId: '',
      busName: '',
      busVehicleNo: '',
      start_city_id: '',
      arrived_city_id: '',
      // departureTime: "",
      roadId: "",
      arrivalTime: "",
      scheduleDate: new Date(),
      price: 0,
      totalSeats: 0
    };
    this.errorMessage = '';
  }

  ngOnInit() {
    this.getAllRoads();
    this.getAllBus();
  }

  getAllRoads() {
    const token = localStorage.getItem('userToken');
    if (!token) {
      this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });

    this.http.get<ApiResponse<any>>('http://localhost:8000/api/roads', {headers})
      .subscribe({
        next: (roads: ApiResponse<any>) => {
          console.log(roads?.data);
          this.roadList = roads?.data;
        }
      });
  }

  getAllBus() {
    const token = localStorage.getItem('userToken');
    if (!token) {
      this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }
    const headers = new HttpHeaders({
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json'
    });

    this.http.get<ApiResponse<any>>('http://localhost:8000/api/buses', {headers})
      .subscribe({
        next: (bus: ApiResponse<any>) => {
          this.busList = bus?.data;
        }
      });
  }

  onBusChange(busId: string): void {
    this.selectedBus = this.busList.find(bus => bus.id === busId);
  }

  onRoadChange(roadId: string): void {
    this.busSchedule.start_city_id = this.roadList.find(road => road.id === roadId)?.start_city.id ?? "";
    this.busSchedule.arrived_city_id = this.roadList.find(road => road.id === roadId)?.arrived_city.id ?? "";
   this.getOnlyHour(this.roadList.find(road => road.id === roadId)?.estimated_time ?? "" );
  }

  getOnlyHour(time: string): void {
    const date = new Date(time);
    console.log(date.toISOString().substring(11, 16).split(':'));
    // return date.toISOString().substring(11, 16).split(':'); // extrait "HH:mm"
    this.arrivalTimeOurs = Number(date.toISOString().substring(11, 16).split(':')[0]);
    this.arrivalTimeMinutes = Number(date.toISOString().substring(11, 16).split(':')[1]);
  }

  toggleView() {
    this.showPostForm = !this.showPostForm;
    this.getVendorBuses();
  }

  getVendorBuses() {
    const token = localStorage.getItem('userToken');
    if (!token) {
      this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }

    this.searchService.getSchedulesByVendorId(this.busSchedule.vendorId, token).subscribe({
      next: (schedules) => {
        this.vendorBuses = schedules;
        console.log('Vendor buses:', this.vendorBuses);

        // Précharger les sièges disponibles
        schedules.forEach((bus) => {
          this.searchService.getBookedSeats(bus.id).subscribe((booked: number[]) => {
            this.availableSeatsMap[bus.id] = bus.totalSeats - booked.length;
          });
        });
      },
      error: (err) => console.error('Error fetching vendor buses:', err)
    });
  }

  editSchedule(scheduleId: number): void {
    this.searchService.getBusScheduleById2(scheduleId).subscribe({
      next: (schedule) => {
        this.busScheduleEdit = schedule;
        // Simule un clic sur le bouton pour ouvrir le modal
        console.log('Schedule to edit:', this.busScheduleEdit);
        this.openEditModalBtn.nativeElement.click();
        console.log('Edit modal opened');
      },
      error: (err) => {
        console.error('Failed to load schedule', err);
      }
    });
  }

  deleteSchedule(scheduleId: number): void {
    this.selectedScheduleId = scheduleId;
    this.openDeleteModalBtn.nativeElement.click(); // Ouvre le modal via le bouton caché
  }


  getScheduleById(vendorId: number) {
    this.searchService.getBusScheduleById2(vendorId).subscribe({
      next: (response) => {
        console.log('Schedule details:', response);
        this.busScheduleEdit = response;
      },
      error: (error) => {
        console.error('Error fetching schedule details:', error);
      }
    });
  }

  updateSchedule() {

    this.busScheduleEdit.scheduleId = 0;
    console.log('Updating schedule with ID:', this.busScheduleEdit.scheduleId);
/*    this.searchService.createBusSchedule(this.busScheduleEdit).subscribe({
      next: (response) => {
        console.log('Schedule updated successfully:', response);
        this.getVendorBuses();
      },
      error: (error) => {
        console.error('Error updating schedule:', error);
      }
    });*/
  }

  confirmDelete(): void {
    this.searchService.deleteSchedule(this.selectedScheduleId).subscribe({
      next: () => {
        this.getVendorBuses(); // Recharge les données
      },
      error: (err) => {
        console.error('Failed to delete schedule', err);
      }
    });
  }


  protected readonly print = print;
}
