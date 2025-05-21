import { ComponentFixture, TestBed } from '@angular/core/testing';

import { NewLocalisationComponent } from './new-localisation.component';

describe('NewLocalisationComponent', () => {
  let component: NewLocalisationComponent;
  let fixture: ComponentFixture<NewLocalisationComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [NewLocalisationComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(NewLocalisationComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
