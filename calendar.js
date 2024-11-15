
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.reschedule-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const appointmentDate = this.dataset.id;
            const appointmentID = this.closest('tr').querySelector('.appointment-id').value;

            const calendarAppointment = new CalendarAppointment(appointmentID, "", "","",therapistID,"","")

            const calendar = new GenericCalendar(appointmentDate, calendarAppointment);
            calendar.create();
        });
    });
});
