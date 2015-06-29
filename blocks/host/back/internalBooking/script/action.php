<script>

		function bookingValidate(option){ 

			$.ajax({
				type: 'GET',
				url: '<?=$formSaraDataAction?>&optionprocess='+option,
				async: false,
				data:$("#form-booking").serialize(),
				success: function(respuesta) {
					myJson=$.parseJSON(respuesta);
					
					if(myJson.status=="false"){
							alert(myJson.mensaje); 
					}
					if(option=="prebooking"){
						$("#prebooking").val(myJson.idbooking);
						$("#valueBooking").val(myJson.value);
					}
					if(option=="book"){
						if(myJson.status=="true"){
							alert(myJson.mensaje); 
							$(".booking-button").hide();
						}
					}
				}
			});

		}


		function updateRoomFriends(){

			$.ajax({
				type: 'GET',
				url: '<?=$formSaraDataHtmlFriend?>',
				async: false,
				data: { 
					guestBooking:$("#guestBooking").val(),
					kids:$("#kids").val(),
				},
				success: function(respuesta) {
					$("#friend-div").html(respuesta);
				}
			});

			

		}
		
		
		function updateDataCustomer(){
			if($.trim($("#idCustomer").val())!=""){
				$.ajax({
					type: 'GET',
					url: '<?=$formSaraDataHtmlCustomer?>',
					async: false,
					data: { 
						idCustomer:$("#idCustomer").val(),
					},
					success: function(respuesta) {
						$("#dataCustomer").html(respuesta);
					}
				});
			}
		}
		

</script>