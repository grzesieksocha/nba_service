$(document).ready(function(){
    var optionsButton = $(".additional-options");
    var optionsDiv = $("#additional-options-div");
    var clicked = false;

    $("form label[for^='league_isPrivate_']").addClass('private-checkbox');

    $("input:radio").change(function () {
        if (this.id === 'league_isPrivate_1') {
            $("#password-row").toggle();
            $("#ask-for-passwd").toggle();
        } else {
            $("#password-row").toggle();
            $("#ask-for-passwd").toggle();
        }
    });

    optionsButton.click(function () {
        if (clicked == false) {
            clicked = true
        }

        if (optionsButton.html() == "Show options") {
            optionsButton.html("Hide options");
            optionsDiv.show();
        } else {
            optionsButton.html("Show options");
            optionsDiv.hide();
        }
    })
});