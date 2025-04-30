$(document).ready(function(){
	$.ajaxSetup({
      headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
       }
   });

	$('.select2').select2()

    $('.select2bs4').select2({
      theme: 'bootstrap4'
    }) 
   

   //$('.dropify').dropify();


   // $('#description').summernote(
   //    {
   //      height: 150,
   //      focus: false
   //    }
   //  );


   $('#checkAll').click(function () {    
      $('.category_ids').prop('checked', this.checked);
      $('.zone_ids').prop('checked', this.checked);
      $('.price_ids').prop('checked', this.checked);
      $('.customer_ids').prop('checked', this.checked);
      $('.booking_ids').prop('checked', this.checked);
    });


   //ensure numeric input 
    $(document).on("input", ".numericInput", function () {
        // Get the entered value
        var enteredValue = $(this).val();


        // Check if the entered value is a valid number (float or integer)
        if (!isValidNumber(enteredValue)) {
            // If not valid, remove the last character
            $(this).val($(this).val().slice(0, -1));
        }
    });


    function isValidNumber(value) {
      var regex = /^-?\d*\.?\d*$/;
      return regex.test(value);
    }



  

});