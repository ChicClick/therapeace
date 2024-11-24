$(document).ready(function() {

    $('.nav-link').click(function(e) {
        e.preventDefault();

        var targetPage = $(this).data('target');

        $.ajax({
            url: targetPage,
            type: 'GET',
            success: function(response) {
                $('#content').html(response);
            }
        });
    });
});
