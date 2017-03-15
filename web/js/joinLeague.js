$(document).ready(function () {
    var joinButton = $(".league-joiner");

    joinButton.click(function () {
        var leagueId = $(this).attr('data-league');
        var leagueAccess = $(this).attr('data-access');
        var leagueName = $(this).parent().prev().children().html();
        if (leagueAccess === 'private') {
            bootbox.prompt({
                title: "Please provide a password for <strong>" + leagueName + "</strong>",
                inputType: 'password',
                callback: function (password) {
                    if (password != null && password != '') {
                        joinLeagueAjax(leagueId, password);
                    }
                }
            });
        } else {
            joinLeagueAjax(leagueId, undefined);
        }
    });

    function joinLeagueAjax(leagueId, password) {
        $.ajax({
            url: Routing.generate('join_league_ajax'),
            type: 'GET',
            data: {
                leagueId: leagueId,
                password: password
            },
            success: function () {
                location.reload();
            },
            error: function () {
                console.log('Joining league failed');
            }
        });
    }
});