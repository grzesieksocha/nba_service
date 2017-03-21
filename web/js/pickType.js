$(document).ready(function(){
    var pickDate = $("#pick_date");
    var pickMatch = $("#pick_match");
    var pickPlayer = $("#pick_player");
    var pickLeague = $("#pick_league");
    var submit = $("#submit-button");

    pickLeague.change(function () {
       $(this).css('background-color', '#00cc00');
    });

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
                    $(pickDate).css('background-color', '#00cc00');
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
                            $(pickPlayer).append('<option disabled="disabled"><strong>' + key + '</strong></option>');
                            $.each(value, function (key, value) {
                                $(pickPlayer).append('<option value="' + key + '">&nbsp;&nbsp;&nbsp;&nbsp;' + value + '</option>');
                            })
                        });
                        $(pickPlayer).prop('disabled', false);
                        $(pickMatch).css('background-color', '#00cc00');
                    } else {
                        $(pickPlayer).append('<option>No available players :(</option>');
                    }
                },
                error: function () {
                    console.log('ERROR');
                }
            });
        }
    });

    pickPlayer.change(function () {
        submit.attr('disabled', false);
    });

    submit.click(function (event) {
        console.log('pushed');
        $.ajax({
            url: Routing.generate('ajax_validate_pick'),
            type: 'GET',
            data: {
                'leagueId': pickLeague.val(),
                'dateId': pickDate.val(),
                'matchId': pickMatch.val(),
                'playerId': pickPlayer.val()
            },
            success: function (result) {
                console.log('aaaaa')
            },
            error: function () {
                event.preventDefault();
            }
        });
    })
});