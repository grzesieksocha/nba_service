$(document).ready(function(){
    var pickDate = $("#pick_date");
    var pickMatch = $("#pick_match");
    var pickPlayer = $("#pick_player");

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

    pickMatch.change(function () {
        $(pickPlayer).prop('disabled', true);
        $.ajax({
            url: Routing.generate('ajax_get_players'),
            type: 'GET',
            data: pickMatch.val(),
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