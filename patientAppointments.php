
    <div class="wrapper">
        <!-- Appointments Tab -->
        <section id="appointments" class="active">
            <h1>APPOINTMENTS</h1>
            <hr>
            <div class="search-sort-container">
                <div class="search-wrapper">
                    <i class="fas fa-search search-icon"></i>
                    <input type="text" placeholder="Search..." id="appointmentsSearch" class="search-bar" onkeyup="searchAppointments()">
                </div>
            </div>
            <generic-table data='patient_upcoming_appointments' reschedule="true" admin="false"></generic-table>

        </section>
            <!-- Reschedule Popup -->
            <generic-calendar></generic-calendar>
            <generic-message-popup></generic-message-popup>
            <notes-popup></notes-popup>
    </div>


