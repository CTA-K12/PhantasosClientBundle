var intv;
intv = setInterval(
    function() {
        $.ajax({
            url: MEDIA_INFO_ROUTE,
            method: 'GET'
        }).done(function(data) {
            // Check if ready (if so, refresh the page)
            if ('Ready' === data.status) {
                window.location.reload();
            } else {
                // Set the status name
                $('#processing-status').html(data.status);

                // If processing add a processing bar
                if ('Processing' === data.status) {
                    $('#processing-progress').html(
                        '<div class="progress">' +
                        '<div class="progress-bar progress-bar-striped active" role="progressbar" valuemax="100"' +
                        ' style="width: ' + data.processing_percentage + '%;">' +
                        Math.round(data.processing_percentage) + '%' +
                        '</div>' +
                        '</div>'
                    );
                }
            }
        });
    },
    1000
);
