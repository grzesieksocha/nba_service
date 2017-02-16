$(document).ready(function(){
    var pickDate = $("#pick_date");
    var pickMatch = $("#pick_match");
    var pickPlayer = $("#pick_player");
    var pickLeague = $("#pick_league");

    pickDate.change(function () {
        $(pickMatch).prop('disabled', true);
            $.ajax({
                url: Routing.generate('ajax_get_matches'),
                type: 'GET',
                data: pickDate.val(),
                success: function (result) {
                    $(pickMatch).empty();
                    $.each(result, function(key, value) {
                        $(pickMatch).append('<option value="' + key + '">' + value + '</option>');
                    });
                    $(pickMatch).prop('disabled', false);
                },
                error: function () {
                    console.log('ERROR');
                }
            });
    });

    pickMatch.change(function () {
        if (pickMatch.val() == '0' || pickLeague.val() === '') {
            $(pickPlayer).prop('disabled', true);
        } else {
            $(pickPlayer).prop('disabled', true);
            $.ajax({
                url: Routing.generate('ajax_get_players'),
                type: 'GET',
                data: {'matchId': pickMatch.val(), 'leagueId': pickLeague.val()},
                success: function (result) {
                    $(pickPlayer).empty();
                    if (false === $.isEmptyObject(result)) {
                        $.each(result, function(key, value) {
                            $(pickPlayer).append('<option value="' + key + '">' + value + '</option>');
                        });
                        $(pickPlayer).prop('disabled', false);
                    } else {
                        $(pickPlayer).append('<option>No available players :(</option>');
                    }
                },
                error: function () {
                    console.log('ERROR');
                }
            });
        }
    })
});