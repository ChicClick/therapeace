// fetching parentID
$(document).ready(function() {
    $('#parentID').on('click', function() {
        // Only fetch data if the dropdown is empty
        if ($('#parentID option').length === 1) {
            $.ajax({
                url: 'a_fetch_parent.php', // Path to your PHP file
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Clear existing options
                    $('#parentID').find('option:not(:first)').remove();
                    // Append new options
                    $.each(data, function(index, parent) {
                        $('#parentID').append($('<option>', {
                            value: parent.parentID, // Use parentID as the value
                            text: parent.parentName // Use parentName as the display text
                        }));
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching parent:', textStatus, errorThrown);
                }
            });
        }
    });
});

/*
$(document).ready(function() {
    $('#therapist').on('click', function() {
        // Only fetch data if the dropdown is empty
        if ($('#therapist option').length === 1) {
            $.ajax({
                url: 'a_fetch_therapist.php', // Path to your PHP file
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Clear existing options
                    $('#therapist').find('option:not(:first)').remove();
                    // Append new options
                    $.each(data, function(index, therapist) {
                        $('#therapist').append($('<option>', {
                            value: therapist.therapistID, // Use therapistID as the value
                            text: therapist.therapistName // Use therapistName as the display text
                        }));
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching therapists:', textStatus, errorThrown);
                }
            });
        }
    });
});
*/


// Fetching serviceName for autofill
$(document).ready(function() {
    $('#service').on('click', function() {
        // Only fetch data if the dropdown is empty
        if ($('#service option').length === 1) {
            $.ajax({
                url: 'a_fetchRegister_service.php', // Path to your PHP file
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    // Clear existing options
                    $('#service').find('option:not(:first)').remove();
                    // Append new options
                    $.each(data, function(index, service) {
                        $('#service').append($('<option>', {
                            value: service.serviceID, // Use serviceID as the value
                            text: service.serviceName // Use serviceName as the display text
                        }));
                    });
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error fetching services:', textStatus, errorThrown);
                }
            });
        }
    });
});

