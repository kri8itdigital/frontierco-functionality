(function( $ ) {

	function HANDLE_POPULATE(STOREADDRESS){

		if(jQuery('#shipping_address_1').length && jQuery('#ship-to-different-address-checkbox').is(':checked')){
			$_TYPE = 'shipping';
		}else{
			$_TYPE = 'billing';
		}

		jQuery('#'+$_TYPE+'_address_1').val(STOREADDRESS.shipping_address_1).trigger('change');
  		jQuery('#'+$_TYPE+'_address_1').closest('.form-row').addClass('fc-readonly');

  		jQuery('#'+$_TYPE+'_address_2').val(STOREADDRESS.shipping_address_2).trigger('change');
  		jQuery('#'+$_TYPE+'_address_2').closest('.form-row').addClass('fc-readonly');
		
		jQuery('#'+$_TYPE+'_city').val(STOREADDRESS.shipping_city).trigger('change');
  		jQuery('#'+$_TYPE+'_city').closest('.form-row').addClass('fc-readonly');
		
		jQuery('#'+$_TYPE+'_postcode').val(STOREADDRESS.shipping_postcode).trigger('change');
  		jQuery('#'+$_TYPE+'_postcode').closest('.form-row').addClass('fc-readonly');
		
		jQuery('#'+$_TYPE+'_country').val(STOREADDRESS.shipping_country).trigger('change');
  		jQuery('#'+$_TYPE+'_country').closest('.form-row').addClass('fc-readonly');
		
		jQuery('#'+$_TYPE+'_state').val(STOREADDRESS.shipping_state).trigger('change');
  		jQuery('#'+$_TYPE+'_state').closest('.form-row').addClass('fc-readonly');
	}


	function HANDLE_CLEAN(){

		if(jQuery('#shipping_address_1').length && jQuery('#ship-to-different-address-checkbox').is(':checked')){
			$_TYPE = 'shipping';
		}else{
			$_TYPE = 'billing';
		}
		
		jQuery('#'+$_TYPE+'_address_1').closest('.form-row').removeClass('fc-readonly');
		jQuery('#'+$_TYPE+'_address_2').closest('.form-row').removeClass('fc-readonly');		
		jQuery('#'+$_TYPE+'_city').closest('.form-row').removeClass('fc-readonly');		
		jQuery('#'+$_TYPE+'_postcode').closest('.form-row').removeClass('fc-readonly');		
		jQuery('#'+$_TYPE+'_country').closest('.form-row').removeClass('fc-readonly');		
		jQuery('#'+$_TYPE+'_state').closest('.form-row').removeClass('fc-readonly');

		if(jQuery('body').hasClass('FCPICKUP')){
			jQuery('#'+$_TYPE+'_address_1').val('').trigger('change');
			jQuery('#'+$_TYPE+'_address_2').val('').trigger('change');
			jQuery('#'+$_TYPE+'_city').val('').trigger('change');
			jQuery('#'+$_TYPE+'_postcode').val('').trigger('change');
			//jQuery('#'+$_TYPE+'_country').val('').trigger('change');
			jQuery('#'+$_TYPE+'_state').val('').trigger('change');
		}

		jQuery('body').removeClass('FCPICKUP');

	}


	function HANDLE_SWAP(){

		jQuery('#customer_details').block({
		    message: null,
		    overlayCSS: {
		        cursor: 'none',
		        background: '#fff',
		        opacity: 0.6
		    }
		});

		$_SWAP_TO = false;
		$_SWAP_FROM = false;

		if(jQuery('#billing_address_1').closest('.form-row').hasClass('fc-readonly')){

			$_SWAP_TO = 'shipping';
			$_SWAP_FROM = 'billing';

		}else{

			$_SWAP_TO = 'billing';
			$_SWAP_FROM = 'shipping';

		}

		jQuery('#'+$_SWAP_TO+'_address_1').val(jQuery('#'+$_SWAP_FROM+'_address_1').val()).trigger('change');
		jQuery('#'+$_SWAP_TO+'_address_1').closest('.form-row').addClass('fc-readonly');

		jQuery('#'+$_SWAP_TO+'_address_2').val(jQuery('#'+$_SWAP_FROM+'_address_2').val()).trigger('change');
		jQuery('#'+$_SWAP_TO+'_address_2').closest('.form-row').addClass('fc-readonly');

		jQuery('#'+$_SWAP_TO+'_city').val(jQuery('#'+$_SWAP_FROM+'_city').val()).trigger('change');
		jQuery('#'+$_SWAP_TO+'_city').closest('.form-row').addClass('fc-readonly');

		jQuery('#'+$_SWAP_TO+'_postcode').val(jQuery('#'+$_SWAP_FROM+'_postcode').val()).trigger('change');
		jQuery('#'+$_SWAP_TO+'_postcode').closest('.form-row').addClass('fc-readonly');

		jQuery('#'+$_SWAP_TO+'_country').val(jQuery('#'+$_SWAP_FROM+'_country').val()).trigger('change');
		jQuery('#'+$_SWAP_TO+'_country').closest('.form-row').addClass('fc-readonly');

		jQuery('#'+$_SWAP_TO+'_state').val(jQuery('#'+$_SWAP_FROM+'_state').val()).trigger('change');
		jQuery('#'+$_SWAP_TO+'_state').closest('.form-row').addClass('fc-readonly');



		jQuery('#'+$_SWAP_FROM+'_address_1').val('').trigger('change');
		jQuery('#'+$_SWAP_FROM+'_address_1').closest('.form-row').removeClass('fc-readonly');

		jQuery('#'+$_SWAP_FROM+'_address_2').val('').trigger('change');
		jQuery('#'+$_SWAP_FROM+'_address_2').closest('.form-row').removeClass('fc-readonly');

		jQuery('#'+$_SWAP_FROM+'_city').val('').trigger('change');
		jQuery('#'+$_SWAP_FROM+'_city').closest('.form-row').removeClass('fc-readonly');

		jQuery('#'+$_SWAP_FROM+'_postcode').val('').trigger('change');
		jQuery('#'+$_SWAP_FROM+'_postcode').closest('.form-row').removeClass('fc-readonly');

		//jQuery('#'+$_SWAP_FROM+'_country').val('').trigger('change');
		jQuery('#'+$_SWAP_FROM+'_country').closest('.form-row').removeClass('fc-readonly');

		jQuery('#'+$_SWAP_FROM+'_state').val('').trigger('change');
		jQuery('#'+$_SWAP_FROM+'_state').closest('.form-row').removeClass('fc-readonly');

		jQuery('#customer_details').unblock();

	}






	jQuery(document).on( 'change', 'input.shipping_method', function() {
			    
		if(jQuery(this).val().indexOf("frontierco_store_pickup") === -1){

			HANDLE_CLEAN();	

		}

	});	





	jQuery(document).on('change', '#frontierco_store_pickup', function(){

		$_STORE = jQuery('#frontierco_store_pickup').val();

		var ajax_data = {
	        action: 'frontierco_selected_store_pickup',
	    	store: $_STORE,
      	};	

      	if($_STORE == ''){

      		HANDLE_CLEAN();

      	}else{

      		jQuery.ajax({
              url: frontierco_params.ajax_url,
              type:'POST',
              data: ajax_data,
              dataType: 'json',
              beforeSend:function(){
               jQuery('#customer_details').block({
				    message: null,
				    overlayCSS: {
				        cursor: 'none',
				        background: '#fff',
				        opacity: 0.6
				    }
				});
              },
              success: function (STOREADDRESS) {

              	jQuery('body').addClass('FCPICKUP');

              	HANDLE_POPULATE(STOREADDRESS);
              
              	jQuery('#customer_details').unblock();

              }
          });

      	}

      	 

	});






	jQuery(document).on('change', '#ship-to-different-address-checkbox', function(){

		if(jQuery('body').hasClass('FCPICKUP')){

			HANDLE_SWAP();

		}

	});
	






	





})( jQuery );
