$(document).ready(function(){
    var pickDate = $("#pick_date");
    var pickMatch = $("#pick_match");

    pickDate.change(function () {
        $(pickMatch).prop('disabled', true);
        $.ajax({
            url: Routing.generate('ajax_get_matches'),
            type: 'GET',
            data: pickDate.val(),
            success: function (html) {
                $(pickMatch).empty();
                $.each(html, function(key, value) {
                    $(pickMatch).append('<option value="' + key + '">' + value + '</option>');
                    console.log(key + ' ' + value);
                });
                $(pickMatch).prop('disabled', false);
            },
            error: function () {
                console.log('ERROR');
            }
        });
    })
});