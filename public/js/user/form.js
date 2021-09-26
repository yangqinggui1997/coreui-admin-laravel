const getIp = callback => {
    callback(country)
}

$(function(){
    
    const phoneInputField = document.getElementById("phone");
    const phoneInput = window.intlTelInput(phoneInputField, {
        utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
        // geoIpLookup: getIp,
        // initialCountry: "auto",
        preferredCountries: ["tw", "vn"],
        separateDialCode: true
    });

    $("#phone").on("keydown", function(evt) {
        evt = evt ? evt : window.event
        var charCode = parseInt(evt.key)
        if ([1, 2, 3, 4, 5, 6, 7, 8, 9].includes(charCode) || evt.code === 'Backspace' || evt.code === 'Delete') return true
        return false
    });

    $('#phone').on('input', function(evt){
        $('#inputPhone').val(phoneInput.getNumber())
    })

    phoneInput.setCountry(country)
    phoneInput.setNumber(oldPhone)

    function handlerSelect() {
        const selectedOptions = $('#groupUserSelect option:selected');
        $('input[name="groupUser[]"]').length && $('input[name="groupUser[]"]').remove();
        selectedOptions.each(function(){
            $('#groupUserSelect').before(`<input type="hidden" name="groupUser[]" value="${$(this).val()}">`)
        })
    }

    if($('#groupUserSelect').length)
        $('#groupUserSelect').multiselect({
            enableFiltering: true,
            filterBehavior: 'both',
            includeSelectAllOption: true,
            onChange: () => handlerSelect(),
            onSelectAll: () => handlerSelect(),
            onDeselectAll:() => handlerSelect()
        });

    if(typeof groupUser !== 'undefined' && groupUser.length)
    {
        $('#groupUserSelect').val(groupUser);
        $('#groupUserSelect').multiselect('refresh');
    }
})