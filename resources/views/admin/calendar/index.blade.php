@extends('layouts.admin')

@section('title', 'Admin Dashboard - TESDA')
@section('page-title', 'Calendar')

@section('content')

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Training & Assessment Calendar</h6>
                    <div>
                        <span class="badge bg-success me-2">
                            <i class="bi bi-square-fill"></i> Training
                        </span>
                        <span class="badge bg-warning me-2">
                            <i class="bi bi-square-fill"></i> Intensive Review
                        </span>
                        <span class="badge bg-primary">
                            <i class="bi bi-square-fill"></i> Assessment
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Event Details Modal -->
    <div class="modal fade" id="eventModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="eventModalLabel">Event Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="eventDetails">
                    <!-- Event details will be loaded here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    #calendar {
        max-width: 100%;
        margin: 0 auto;
        min-height: 600px;
    }
    
    .fc-event {
        cursor: pointer;
        font-size: 0.85rem;
        padding: 2px 4px;
    }
    
    .fc-daygrid-event {
        white-space: normal !important;
        align-items: normal !important;
    }
    
    .fc-event-title {
        font-weight: 500;
    }
    
    .fc-toolbar-title {
        font-size: 1.5rem !important;
        font-weight: 600;
    }
    
    .fc-button {
        text-transform: capitalize !important;
    }
    
    /* Ensure events are visible */
    .fc-daygrid-event-harness {
        margin-bottom: 2px;
    }
</style>
@endpush

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Initializing calendar...');
    
    var calendarEl = document.getElementById('calendar');
    
    if (!calendarEl) {
        console.error('Calendar element not found!');
        return;
    }
    
    var events = @json($events ?? []);
    
    console.log('Events data:', events);
    console.log('Total events:', events.length);
    
    try {
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,dayGridWeek'
            },
            events: events,
            eventDisplay: 'block',
            displayEventTime: false,
            eventClick: function(info) {
                info.jsEvent.preventDefault();
                
                let eventType = info.event.extendedProps.type;
                let typeLabel = eventType === 'training' ? 'Training' : 
                               eventType === 'assessment' ? 'Assessment' : 
                               'Intensive Review';
                
                let details = `
                    <div class="mb-3">
                        <strong>Event:</strong> ${info.event.title}
                    </div>
                    <div class="mb-3">
                        <strong>Type:</strong> <span class="badge bg-${eventType === 'training' ? 'success' : eventType === 'assessment' ? 'primary' : 'warning'}">${typeLabel}</span>
                    </div>
                    <div class="mb-3">
                        <strong>Date:</strong> ${info.event.start.toLocaleDateString('en-US', { 
                            year: 'numeric', 
                            month: 'long', 
                            day: 'numeric' 
                        })}
                    </div>
                `;
                
                if (info.event.extendedProps.venue) {
                    details += `<div class="mb-3"><strong>Venue:</strong> ${info.event.extendedProps.venue}</div>`;
                }
                
                if (info.event.extendedProps.instructor) {
                    details += `<div class="mb-3"><strong>Instructor:</strong> ${info.event.extendedProps.instructor}</div>`;
                }
                
                if (info.event.extendedProps.assessor_name) {
                    details += `<div class="mb-3"><strong>Assessor:</strong> ${info.event.extendedProps.assessor_name}</div>`;
                }
                
                document.getElementById('eventDetails').innerHTML = details;
                
                var modal = new bootstrap.Modal(document.getElementById('eventModal'));
                modal.show();
            },
            eventDidMount: function(info) {
                info.el.title = info.event.title;
                console.log('Event mounted:', info.event.title);
            },
            height: 'auto',
            contentHeight: 650,
            aspectRatio: 1.8,
            eventColor: '#378006'
        });
        
        calendar.render();
        console.log('Calendar rendered successfully');
        console.log('Calendar object:', calendar);
        
    } catch (error) {
        console.error('Error initializing calendar:', error);
    }
});
</script>
@endpush

