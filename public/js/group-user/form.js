$(function(){
    $('#reset').on("click", function(evt) { 
        evt.preventDefault();
        $("#name").val('');
    });
});