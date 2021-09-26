$(function() {
    ClassicEditor.create( document.querySelector( '#content' ), {
        // plugins: [ CKFinder ],
        // Enable the CKFinder button in the toolbar.
        toolbar: {
            items: [
                'heading', '|',
                'alignment', '|',
                'bold', 'italic', 'strikethrough', 'underline', 'subscript', 'superscript', '|',
                'link', '|',
                'bulletedList', 'numberedList', 'todoList',
                '-', // break point
                'fontfamily', 'fontsize', 'fontColor', 'fontBackgroundColor', '|',
                'code', 'codeBlock', '|',
                'insertTable', '|',
                'outdent', 'indent', '|',
                'uploadImage', 'mediaEmbed', 'blockQuote', '|',
                'undo', 'redo', '|',
                'ckfinder'
            ],
            // shouldNotGroupWhenFull: true
        },
        toolbarLocation: 'bottom',
        ckfinder: {
            uploadUrl: ckEditorUploadUrl
        },
    })
    .catch( error => {
        console.error( error );
    });

    $('#reset').on("click", function(evt) {
        evt.preventDefault();
        $("#title").val('');
        $('#link').val('');
        $('#author').val('');
        $('#postCategory').val(0);
        $('#pageLinkName').val('');
    });

    $("#thumbnail").on('change', function(event) {
        var files = this.files;
        if (files.length)
            $("#thumbImage").attr('src', URL.createObjectURL(event.target.files[0]));
        else 
            $("#thumbImage").attr('src', thumbUrl);
    });

    const options = {
        title : "Select parent category",
        data: data,
        maxHeight: 3000,
        selectChildren : false,
        clickHandler: function(element){
            $("#categoryParentDropdown").SetTitle($(element).find("a").first().text());
            $('#categoryParent').val($(element).attr('data-id'));
            $('#categoryParentName').val($(element).find("a").first().text());
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

    function handlerSelect() {
        const selectedOptions = $('#groupUserSelect option:selected');
        $('input[name="groupUser[]"]').length && $('input[name="groupUser[]"]').remove();
        selectedOptions.each(function(){
            $('#groupUserSelect').before(`<input type="hidden" name="groupUser[]" value="${$(this).val()}">`)
        })
    }

    $('#groupUserSelect').multiselect({
        enableFiltering: true,
        filterBehavior: 'both',
        includeSelectAllOption: true,
        onChange: () => handlerSelect(),
        onSelectAll: () => handlerSelect(),
        onDeselectAll:() => handlerSelect()
    });

    if(groupUser)
    {
        $('#groupUserSelect').val(groupUser);
        $('#groupUserSelect').multiselect('refresh');
    }
});