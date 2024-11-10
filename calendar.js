
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.reschedule-link').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const appointmentDate = this.dataset.id;
            const appointmentID = this.closest('tr').querySelector('.appointment-id').value;
            const calendar = new GenericCalendar(appointmentDate, appointmentID, therapistID);
            calendar.create();
        });
    });
});
