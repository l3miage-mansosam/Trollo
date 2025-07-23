import {Component, EventEmitter, inject, Output} from '@angular/core';
import {FormsModule} from '@angular/forms';
import {CommonModule} from '@angular/common';
import {BusSchedule, Search, ISearchBus, ApiResponse, Bus, Road, User, Booking} from '../../model/model';
import {SearchService} from '../../service/search.service';
import {Router} from '@angular/router';
import {HttpClient, HttpHeaders} from '@angular/common/http';
import {OnInit} from '@angular/core';
import {ViewChild, ElementRef} from '@angular/core';
import {LoadingComponent} from '../../shared/loading/loading.component';


@Component({
  selector: 'app-schedule',
  imports: [CommonModule, FormsModule, LoadingComponent],
  templateUrl: './schedule.component.html',
  styleUrl: './schedule.component.css'
})
export class ScheduleComponent implements OnInit {
  @ViewChild('openEditModalBtn') openEditModalBtn!: ElementRef;
  @ViewChild('openDeleteModalBtn') openDeleteModalBtn!: ElementRef;
  @ViewChild('openBookingModalBtn') openBookingModalBtn!: ElementRef;
  // @Output() bookSession = new EventEmitter<string>();

  http = inject(HttpClient);
  roadList: Road[] = [];
  busList: Bus[] = [];
  router = inject(Router);
  showPostForm = false;
  vendorBuses: ISearchBus[] = [];
  availableSeatsMap: { [key: number]: number | undefined } = {};
  arrivalTimeOurs: number = 0;
  arrivalTimeMinutes: number = 0;

  busSchedule: BusSchedule = new BusSchedule();
  busScheduleEdit: ISearchBus = new ISearchBus();

  searchObj: Search = new Search();
  selectedScheduleId: string = "";

  selectedBus: any = new Bus();
  selectedRoad: any = new Road();
  isLoading: boolean = true;
  user: any;

  seats:number = 1;
  bookingData: Booking = new Booking();

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
      scheduleId: "",
      vendorId: 424,
      busId: '',
      busName: '',
      // busVehicleNo: '',
      start_city_id: '',
      arrived_city_id: '',
      // departureTime: "",
      roadId: "",
      arrivalTime: "",
      scheduleDate: "",
      price: 0,
      totalSeats: 0
    };

    this.user = JSON.parse(localStorage.getItem('user') ?? "");
  }

  errorMessage: string = '';

  submitSchedule(dataForm: {
    busId: string;
    roadId: string;
    estimated_time: string;
    departure_date: string;
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
        this.toggleView();
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

      if (this.busSchedule.scheduleId) {
        this.updateSchedule(cleanedSchedule);
      }else {
        this.submitSchedule(cleanedSchedule);
      }
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
      scheduleId: "",
      vendorId: 0,
      busId: '',
      busName: '',
      // busVehicleNo: '',
      start_city_id: '',
      arrived_city_id: '',
      // departureTime: "",
      roadId: "",
      arrivalTime: "",
      scheduleDate: "",
      price: 0,
      totalSeats: 0
    };
    this.errorMessage = '';
  }

  verifyLoadData() {
    if (this.busList.length > 0 && this.roadList.length > 0) {
      this.isLoading = false;
    }
  }

  ngOnInit() {
    this.getAllRoads();
    this.getAllBus();
    this.toggleView();
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
          this.roadList = roads?.data;
          this.verifyLoadData();
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
          this.verifyLoadData()
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

  getOnlyHour(time: string): string[] {
    const date = new Date(time);
    this.arrivalTimeOurs = Number(date.toISOString().substring(11, 16).split(':')[0]);
    this.arrivalTimeMinutes = Number(date.toISOString().substring(11, 16).split(':')[1]);
    return date.toISOString().substring(11, 16).split(':'); // extrait "HH:mm"
  }

  toggleView(sessionId: string = "") {
    this.isLoading = true;
    if (sessionId) {
      this.getScheduleById(sessionId);
    } else {
      this.busScheduleEdit = new ISearchBus();
      this.resetForm();
    }
    this.showPostForm = this.user.role.name === 'ADMIN' ? !this.showPostForm : false;
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
        console.log('Vendor buses:', schedules);
        this.vendorBuses = schedules;
        this.verifyLoadData();

        // Précharger les sièges disponibles
        /*schedules.forEach((bus) => {
          this.searchService.getBookedSeats(bus.id).subscribe((booked: number[]) => {
            this.availableSeatsMap[bus.id] = bus.totalSeats - booked.length;
          });
        });*/
      },
      error: (err) => console.error('Error fetching vendor buses:', err)
    });
  }

  getArrivalDate(startDate: string | Date, estimatedDuration: string): string {
    const departure = new Date(startDate);

    const [hoursStr, minutesStr] = this.getOnlyHour(estimatedDuration);
    const hours = parseInt(hoursStr, 10);
    const minutes = parseInt(minutesStr, 10);

    // Ajout du temps au départ
    departure.setHours(departure.getHours() + hours);
    departure.setMinutes(departure.getMinutes() + minutes);

    return departure.toISOString();
  }

  toDatetimeLocalFormat(date: string | Date): string {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    return `${year}-${month}-${day}T${hours}:${minutes}`;
  }

  editSchedule(scheduleId: string): void {
    const token = localStorage.getItem('userToken');
    if (!token) {
      this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }

    this.searchService.getBusScheduleById2(scheduleId, token).subscribe({
      next: (schedule) => {
        this.busScheduleEdit = schedule;
        // Simule un clic sur le bouton pour ouvrir le modal
        console.log('Schedule to edit:', this.busScheduleEdit);
        /*this.openEditModalBtn.nativeElement.click();
        console.log('Edit modal opened');*/
      },
      error: (err) => {
        console.error('Failed to load schedule', err);
      }
    });
  }

  deleteSchedule(scheduleId: string): void {
    this.selectedScheduleId = scheduleId;
    this.openDeleteModalBtn.nativeElement.click(); // Ouvre le modal via le bouton caché
  }


  getScheduleById(vendorId: string) {
    console.log(vendorId);
    const session = this.vendorBuses.find(session => session.id === vendorId);
        this.busSchedule = {
          scheduleId: session?.id ?? "",
          vendorId: 0,
          busId: session?.bus.id ?? "",
          busName: session?.bus.name ?? "",
          totalSeats: session?.bus.capacity ?? 0,
          roadId: session?.road.id ?? "",
          start_city_id: session?.start_city.id ?? "",
          arrived_city_id: session?.arrived_city.id ?? "",
          scheduleDate: session?.departure_date ? this.toDatetimeLocalFormat(session.departure_date) : "",
          arrivalTime: "",
          price: session?.unit_price ?? 0,
        };
        this.getOnlyHour(session?.estimated_time ?? "");

        console.log(this.busSchedule);
  }

  updateSchedule(
    dataForm: {
      busId: string;
      roadId: string;
      estimated_time: string;
      departure_date: string;
      unit_price: number;
      start_city_id: string;
      arrived_city_id: string
    }
  ) {
    this.isLoading = true;
    const token = localStorage.getItem('userToken');
    if (!token) {
      this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }

    this.searchService.updateBusSchedule(dataForm, this.busSchedule.scheduleId, token).subscribe({
      next: (response) => {
        console.log('Schedule updated successfully:', response);
        this.resetForm();
        this.toggleView();
      },
      error: (error) => {
        console.error('Error updating schedule:', error);
      }
    });
  }

  confirmDelete(): void {
    this.isLoading = true;
    const token = localStorage.getItem('userToken');
    if (!token) {
      this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }
    this.searchService.deleteSchedule(this.selectedScheduleId, token).subscribe({
      next: () => {
        this.getVendorBuses(); // Recharge les données
      },
      error: (err) => {
        console.error('Failed to delete schedule', err);
      }
    });
  }

  onBookClick(sessionId: string): void {
    this.getScheduleById(sessionId);

    this.openBookingModalBtn.nativeElement.click();
  }

  confirmBooking(): void {
    const token = localStorage.getItem('userToken');
    if (!token) {
      this.errorMessage = 'Unauthorized. Please log in.';
      return;
    }
    const confData = {
      user_id: this.user.id,
      session_id: this.busSchedule.scheduleId,
      reservation_date: this.toDatetimeLocalFormat(new Date(Date.now())),
      // reservation_date: new Date(),
      price: (this.busSchedule.price * this.seats)
    }
console.log(confData);
    this.searchService.createBooking(confData, token).subscribe({
      next: () => {
        console.log('Réservation confirmée');
        // tu peux déclencher une notification ou un refresh
      },
      error: (err) => {
        console.error('Erreur de réservation :', err);
      },
    });
  }
  protected readonly print = print;
}
