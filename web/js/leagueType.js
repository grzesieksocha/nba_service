$(document).ready(function(){
    $("form label[for^='league_isPrivate_']").addClass('private-checkbox');
    $("input:radio").change(function () {
        if (this.id === 'league_isPrivate_1') {
            $("#password-row").toggle();
            $("#ask-for-passwd").toggle();
        } else {
            $("#password-row").toggle();
            $("#ask-for-passwd").toggle();
        }
    })
});