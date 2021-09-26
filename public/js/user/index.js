$(function() {
    $('button[data-type="btnDelete"]').on("click", function(evt){
      evt.preventDefault();
      $confirm = confirm("Are you sure want to delete this?");
      if(!$confirm)
        return;
      else
        $(this).parent().submit()
    })
});