
document.addEventListener('DOMContentLoaded', function () {
    const rescheduleButtons = document.querySelectorAll('.reschedule-button');

    rescheduleButtons.forEach(button => {
        button.addEventListener('click', function (e) {
            e.preventDefault();
            const appointmentDate = this.dataset.id;
            const parentElement = this.closest('[data-therapist-id]');
            const therapistId = parentElement ? parentElement.dataset.therapistId : null;

            const calendar = new GenericCalendar(appointmentDate, this.dataset.appointmentId, therapistId);
            calendar.create();
        });
    });
});