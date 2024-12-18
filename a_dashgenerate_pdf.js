document.addEventListener('DOMContentLoaded', () => {
    // Function to load patient and appointment data from the server
    let dashboardData;
    async function loadDashboardData() {
        try {
            const response = await fetch('a_dashboarddata.php');
            dashboardData = await response.json();

            // Display total patients
            if (dashboardData.totalPatients) {
                document.querySelector("h1").innerText = `${dashboardData.totalPatients} Active Patients`;
            } else {
                document.querySelector("h1").innerText = "No patient data available";
            }

            // Display growth percentage
            if (dashboardData.growthPercentage !== undefined) {
                document.querySelector(".growth-percentage").innerText = `${dashboardData.growthPercentage}% vs last week`;
            } else {
                document.querySelector(".growth-percentage").innerText = "No growth data available";
            }

            // Display date range
            if (dashboardData.dateRange) {
                document.querySelector(".date-range").innerText = dashboardData.dateRange;
            } else {
                document.querySelector(".date-range").innerText = "No date range available";
            }

            // Display patient chart data
            if (dashboardData.chartData) {
                const labels = dashboardData.chartData.map(entry => entry.month);
                const patientCounts = dashboardData.chartData.map(entry => entry.patient_count);
                createPatientChart(labels, patientCounts);
            }

            // Display appointment chart data
            if (dashboardData.appointmentData) {
                const appointmentLabels = dashboardData.appointmentData.map(entry => entry.serviceName);
                const appointmentCounts = dashboardData.appointmentData.map(entry => entry.appointment_count);
                createAppointmentChart( appointmentCounts);
            }
        } catch (error) {
            console.error("Error loading dashboard data:", error);
        }
    }

    // Function to create the patient bar chart
    function createPatientChart(labels, data) {
        const ctx = document.getElementById('patientChart').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Patient Count',
                    data: data,
                    backgroundColor: '#ffd700'
                }]
            },
            options: { responsive: true }
        });
    }

    // Function to create the appointment doughnut chart
    async function createAppointmentChart() {
        const ctx = document.getElementById('appointmentChart').getContext('2d');

        try {
            const dashboardResponse = await fetch('./generic-components/table-fetch/admin_get_dashboard.php');
            if (!dashboardResponse.ok) throw new Error(`HTTP error! status ${dashboardResponse.status}`);
            const dashboardData = await dashboardResponse.json();
    
            const serviceCounts = dashboardData.reduce((acc, item) => {
                acc[item.service_name] = (acc[item.service_name] || 0) + 1;
                return acc;
            }, {});

            const serviceResponse = await fetch("z_get_all_services.php");
            if (!serviceResponse.ok) throw new Error(`HTTP error! status ${serviceResponse.status}`);
            const serviceData = await serviceResponse.json();

            const labels = serviceData.map(service => service.serviceName);
            const data = labels.map(label => serviceCounts[label] || 0);
            const backgroundColors = serviceData.map(service => service.serviceColor);
    
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Appointments per Service',
                        data: data,
                        backgroundColor: backgroundColors
                    }]
                },
                options: { responsive: true }
            });
        } catch (e) {
            console.error('Error fetching data or creating chart:', e);
        }
    }
    

    function generateReport() {
        if (!dashboardData) {
            console.error("No data to generate report");
            return;
        }
    
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
    
        // Title Section
        doc.setFontSize(22);
        doc.setFont("helvetica", "bold");
        doc.text("TheraPeace Report", 20, 20);
    
        // Patient Data Table
        const patientTableColumns = ["Metric", "Value"];
        const patientTableRows = [
            ["Total Patients", dashboardData.totalPatients || "N/A"],
            ["Growth Percentage", `${dashboardData.growthPercentage || "N/A"}%`],
            ["Date Range", dashboardData.dateRange || "N/A"]
        ];
    
        doc.autoTable({
            head: [patientTableColumns],
            body: patientTableRows,
            startY: 30,
            theme: "grid",
        });
    
        // Appointment Data Table
        const appointmentTableColumns = ["Service Name", "Appointment Count"];
        const appointmentTableRows = dashboardData.appointmentData.map(entry => [
            entry.serviceName,
            entry.appointment_count,
        ]);
    
        doc.autoTable({
            head: [appointmentTableColumns],
            body: appointmentTableRows,
            startY: doc.lastAutoTable.finalY + 10, // Start below the previous table
            theme: "grid",
        });
    
        // Add Charts (if available)
        setTimeout(() => {
            const patientChart = document.getElementById("patientChart").toDataURL();
            const appointmentChart = document.getElementById("appointmentChart").toDataURL();
    
            const currentY = doc.lastAutoTable.finalY + 20;
    
            // Add Patient Chart
            doc.addImage(patientChart, "PNG", 20, currentY, 80, 50);
    
            // Add Appointment Chart below the Patient Chart
            doc.addImage(appointmentChart, "PNG", 20, currentY + 60, 80, 50);
    
            // Save the PDF
            doc.save("TheraPeace Report.pdf");
        }, 500); // Wait to ensure charts are rendered
    }
    
    // Add event listener to the "Generate Report" button
    document.querySelector('.view-report').addEventListener('click', generateReport);

    // Load dashboard data
    loadDashboardData();
});


function backupData(event = null) {
    // If event is provided (i.e., if the link is clicked), prevent the default action
    if (event) {
        event.preventDefault();
    }
    
    // Open a new window or tab to download the file
    window.location.href = 'admin_backupdata.php'; // PHP script that handles the export
}

