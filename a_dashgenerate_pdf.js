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
    

    // Generate PDF report
    function generateReport() {
        if (!dashboardData) {
            console.error("No data to generate report");
            return;
        }
    
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        let lineHeight = 10;  // Line spacing
        let leftMargin = 20;  // Left margin
        let topMargin = 20;   // Top margin for first line
        let currentY = topMargin;
    
        // Title Section
        doc.setFontSize(22);
        doc.setFont("helvetica", "bold");
        doc.text("TheraPeace Report", leftMargin, currentY);
        currentY += lineHeight + 10; // Extra spacing below title
    
        // Patient Data Section
        doc.setFontSize(16);
        doc.setFont("helvetica", "bold");
        doc.text("Patient Report", leftMargin, currentY);
        currentY += lineHeight;
    
        doc.setFontSize(12);
        doc.setFont("helvetica", "normal");
        doc.text(`Total Patients: ${dashboardData.totalPatients}`, leftMargin, currentY);
        currentY += lineHeight;
    
        // Growth Percentage Section
        if (dashboardData.growthPercentage !== undefined) {
            doc.text(`Growth Percentage: ${dashboardData.growthPercentage}% vs last week`, leftMargin, currentY);
        } else {
            doc.text("Growth Percentage: Data not available", leftMargin, currentY);
        }
        currentY += lineHeight;
    
        // Date Range Section
        if (dashboardData.dateRange) {
            doc.text(`Date Range: ${dashboardData.dateRange}`, leftMargin, currentY);
        } else {
            doc.text("Date Range: Data not available", leftMargin, currentY);
        }
        currentY += lineHeight + 5;  // Extra spacing below section
    
        // Appointment Data Section
        doc.setFontSize(16);
        doc.setFont("helvetica", "bold");
        doc.text("Appointment Report", leftMargin, currentY);
        currentY += lineHeight;
    
        doc.setFontSize(12);
        doc.setFont("helvetica", "normal");
    
        // Appointment data with aligned columns
        dashboardData.appointmentData.forEach((entry, index) => {
            const serviceText = `${index + 1}. ${entry.serviceName}`;
            const countText = `${entry.appointment_count} appointments`;
            doc.text(serviceText, leftMargin, currentY);
            doc.text(countText, leftMargin + 130, currentY);  // Right-align appointment count
            currentY += lineHeight;
        });
        currentY += lineHeight;
    
        // Check if there's enough space for charts, else add a new page
        if (currentY + 100 > doc.internal.pageSize.height) {
            doc.addPage();
            currentY = topMargin;
        }
    
        // Wait for the charts to be rendered before adding images
        setTimeout(() => {
            const patientChart = document.getElementById('patientChart').toDataURL();
            const appointmentChart = document.getElementById('appointmentChart').toDataURL();
    
            // Add Patient Chart Image
            doc.addImage(patientChart, 'PNG', leftMargin, currentY, 80, 50);
            currentY += 60; // Space below the chart image
    
            // Check if we need to add a new page for the second chart
            if (currentY + 50 > doc.internal.pageSize.height) {
                doc.addPage();
                currentY = topMargin;
            }
    
            // Add Appointment Chart Image
            doc.addImage(appointmentChart, 'PNG', leftMargin, currentY, 80, 50);
    
            // Save the PDF
            doc.save('Dashboard_Report.pdf');
        }, 500); // Wait a bit to ensure the chart is rendered
    }
    
    // Add event listener to the "Generate Report" button
    document.querySelector('.view-report').addEventListener('click', generateReport);

    // Load dashboard data
    loadDashboardData();
});
