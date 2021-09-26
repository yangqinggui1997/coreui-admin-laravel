$(function(){
    $('#reset').on("click", function(evt) { 
        evt.preventDefault();
        $("#name").val('');
        $('#categoryParent').val(0);
    });

    $("#thumbnail").on('change', function(event) {
        var files = this.files;
        if (files.length)
            $("#thumbImage").attr('src', URL.createObjectURL(event.target.files[0]));
        else 
            $("#thumbImage").attr('src', thumbUrl);
    });
    
    data.unshift({
        title: "Select parent category", 
        dataAttrs: [
            {
                title: "id",
                data: "" 
            }
        ]
    });

    const options = {
        title : "Select parent category",
        data: data,
        maxHeight: 3000,
        selectChildren : false,
        clickHandler: function(element){
            $("#categoryParentDropdown").SetTitle($(element).find("a").first().text());
            $('#categoryParent').val($(element).attr('data-id'));
            $("#categoryParentDropdown").toggleClass('show');
            $("#categoryDropdownMenu").toggleClass('show');
        },
        closedArrow: '<i class="cil-plus" aria-hidden="true"></i>',
        openedArrow: '<i class="cil-minus" aria-hidden="true"></i>',
        multiSelect: false,
    }

    $("#categoryParentDropdown").DropDownTree(options);

    $('#categoryParentDropdown a').on('click', function(e){
        e.preventDefault();
    });
    
    if($('#categoryParentName').val())
        $('#categoryParentDropdown').SetTitle($('#categoryParentName').val());
});