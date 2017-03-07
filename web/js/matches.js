$(document).ready(function(){
    var dateFrom = $("#dateFromWidget");
    var dateTo = $("#dateToWidget");
    var dateFromWidget = $(".dateFrom");
    var dateToWidget = $(".dateTo");
    var submitButton = $("#submit-dates");
    var matchesTable = $("#table-list-matches");
    var matchesList = $("#match-list-table");
    matchesTable.hide();
    var row = $("<tr></tr>");

    dateFromWidget.datetimepicker({
        format: 'DD MMMM YYYY',
        defaultDate: 'now'
    });
    dateToWidget.datetimepicker({
        format: 'DD MMMM YYYY',
        defaultDate: 'now',
        useCurrent: false
    });
    dateFromWidget.on("dp.change", function (e) {
        dateToWidget.data("DateTimePicker").minDate(e.date);
    });
    dateToWidget.on("dp.change", function (e) {
        dateFromWidget.data("DateTimePicker").maxDate(e.date);
    });

    submitButton.click(function (event) {
        event.preventDefault();
        matchesTable.hide();
        matchesList.empty();
        var timezone = $('input[name=time]:checked').val();
        $(submitButton).prop('disabled', true);
        $.ajax({
            url: Routing.generate('ajax_get_matches_for_list'),
            type: 'GET',
            data: {
                dateFrom: dateFrom.val(),
                dateTo: dateTo.val(),
                timezone: timezone
            },
            success: function (result) {
                $.each(result, function ( i, match ) {
                    row.append("<td><a href=\"/match/" + match["id"] + "\">Show</a></td>");
                    row.append("<td>" + match["date"] + "</td>");
                    row.append("<td>" + match["awayTeam"] + "</td>");
                    row.append("<td>" + match["awayTeamPoints"] + "</td>");
                    row.append("<td><p class=\"at\">@</p></td>");
                    row.append("<td>" + match["homeTeam"] + "</td>");
                    row.append("<td>" + match["homeTeamPoints"] + "</td>");
                    matchesList.append(row);
                    row = $("<tr></tr>");
                });
                matchesTable.show();
            },
            error: function () {
                console.log('ERROR');
            }
        });
    });
});