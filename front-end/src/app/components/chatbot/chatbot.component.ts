import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { ChatbotService } from './chatbot.service';

@Component({
  selector: 'app-chatbot',
  standalone: true,
  templateUrl: './chatbot.component.html',
  styleUrls: ['./chatbot.component.css'],
  imports: [CommonModule, FormsModule]
})
export class ChatbotComponent implements OnInit {

  message = '';
  chatlog: string[] = [];
  routes: any[] = [];
  departureCity = '';
  arrivalCity = '';
  chatState: 'greeting' | 'departure_city' | 'arrival_city' = 'greeting';

  constructor(private chatbotService: ChatbotService) {}

  ngOnInit(): void {
    this.chatbotService.getRoutes().subscribe(data => {
      this.routes = data;
    });
    this.chatlog.push("Bot : Bonjour ! Dites-moi votre ville de départ.");
    this.chatState = 'departure_city';
  }

  normalize(str: string): string {
    return str.normalize("NFD").replace(/[\u0300-\u036f]/g, "").toLowerCase();
  }

  sendMessage() {
    if (!this.message.trim()) { return; }

    const userMessage = this.message.trim();
    this.chatlog.push('You : ' + userMessage);

    if (this.chatState === 'departure_city') {
      const validDeparture = this.routes.some(route =>
        this.normalize(route.from) === this.normalize(userMessage)
      );

      if (!validDeparture) {
        this.chatlog.push("Bot : Ville de départ invalide. Veuillez réessayer.");
        this.message = '';
        return;
      }

      this.departureCity = userMessage;
      this.chatlog.push("Bot : Très bien. Quelle est votre ville d'arrivée ?");
      this.chatState = 'arrival_city';
    } 
    else if (this.chatState === 'arrival_city') {
      if (this.normalize(this.departureCity) === this.normalize(userMessage)) {
        this.chatlog.push("Bot : La ville d'arrivée doit être différente de la ville de départ. Veuillez réessayer.");
        this.message = '';
        return;
      }

      const validArrival = this.routes.some(route =>
        this.normalize(route.to) === this.normalize(userMessage)
      );

      if (!validArrival) {
        this.chatlog.push("Bot : Ville d'arrivée invalide. Veuillez réessayer.");
        this.message = '';
        return;
      }

      this.arrivalCity = userMessage;

      const route = this.routes.find(route =>
        this.normalize(route.from) === this.normalize(this.departureCity) &&
        this.normalize(route.to) === this.normalize(this.arrivalCity)
      );

      if (route) {
        this.chatlog.push(`Bot : Voici les informations pour ${this.departureCity} -> ${this.arrivalCity} :`);
        this.chatlog.push(`Bus Name: ${route.busName}`);
        this.chatlog.push(`Company: ${route.company}`);
        this.chatlog.push(`Departure Time: ${route.departureTime}`);
        this.chatlog.push(`Arrival Time: ${route.arrivalTime}`);
        this.chatlog.push(`Seats Available: ${route.seats}`);
        this.chatlog.push(`Price: ${route.price}`);
      } else {
        this.chatlog.push("Bot : Désolé, aucun bus trouvé pour cet itinéraire.");
      }

      this.chatState = 'greeting';
      this.chatlog.push("Bot : Si vous voulez recommencer, dites-moi votre ville de départ.");
      this.chatState = 'departure_city';
    }

    this.message = '';
  }
}
