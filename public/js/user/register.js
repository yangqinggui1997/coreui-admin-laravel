$(function(){

    function check(element)
    {
        $(element).val().length ? $(element).addClass("is-valid").removeClass("is-invalid") : $(element).remove("is-valid").addClass("is-invalid")
    }

    $("#name").on('input', function(){
        check(this)
    })

    $("#phone").on('input', function(){
        check(this)
    })

    $("#carPlate").on('input', function(){
        check(this)
    })
})