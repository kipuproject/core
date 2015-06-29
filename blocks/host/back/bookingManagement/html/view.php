<script>
	function assignStatus(obj,booking){
	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "assignStatus",
				status:obj.val(),
				booking:booking,
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
			}
		});
	}
	
	function assignStatusPayment(obj,booking){
	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "assignStatusPayment",
				paymentstatus:obj.val(),
				booking:booking,
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
			}
		});
	}
	
	function assignOnlineValue(obj,booking){

	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false, 
			data: { 
				optionBooking : "assignOnlineValue",
				onlinepayment:obj.val(),
				booking:booking,
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
			}
		});
	}
  
	function assignDate(chekininput,chekoutinput,booking){

	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "assignDate",
				chekininput:chekininput.val(),
				chekoutinput:chekoutinput.val(),
				booking:booking,
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
				location.reload(); 
			}
		});
		
		
	}
	
	function assignRoom(obj,booking){
	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "assignRoom",
				room:obj.val(),
				booking:booking,
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
			}
		});
	}
	
	function assignTypeRoom(obj,booking){
	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "assignTypeRoom",
				typeroom:obj.val(),
				booking:booking,
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
			}
		});
	}
	
	
	function assignPaymentValue(obj,booking){

	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "assignPaymentValue",
				value:obj.val(),
				booking:booking,
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
			}
		});
	}	
	
	function assignObservation(obj,booking){
 
	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "assignObservation",
				value:obj.val(),
				booking:booking,
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
			}
		});
	}	
	
	function assignValue(obj,booking){

	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "assignValue",
				value:obj.val(),
				booking:booking,
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
				$('#valueinput').prop('disabled',true);
				$('#valuenightinput').val(($('#valueinput').val())/($('#nights').html())); 
			}
		});
	}
	
	
	function showDetail(){
	
	   var my_books = $('.activeBook');
	   var array_id=[];
	   my_books.each(function(){
			   var $this = $(this); 
			   array_id.push($this.attr('id'));
				
	   }); 

	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "showDetails",
				bookings:array_id.toString(),
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				$("#booking-calendar").html(respuesta);
				$(".btn-calendar").hide();
				$(".return-btn").show();
			}
		});
	}
	
	function unselect(){
	   var my_books = $('.activeBook');
	   var array_id=[];
	   my_books.each(function(){
			   var $this = $(this); 
			   $this.removeClass("activeBook");
			   $this.css({"border":"1px solid #FFFFFF"});
				
	   }); 
	}	
	
	function block(){
	   var my_books = $('.activeBook');
	   var array_id=[];
	   my_books.each(function(){
			   var $this = $(this); 
			   $this.css({"background":"#000000"});
			   array_id.push($this.attr('id'));
				
	   }); 
	   
	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "blockBooking",
				bookings:array_id.toString(),
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
			}
		});
	}		

	function unblock(){
	   var my_books = $('.activeBook');
	   var array_id=[];
	   my_books.each(function(){
			   var $this = $(this); 
			   $this.css({"background":"#FFFFFF"});
			   array_id.push($this.attr('id'));
				
	   }); 
	   
	   $.ajax({
			type: 'GET',
			url: '<?=$formSaraDataURL?>',
			async: false,
			data: { 
				optionBooking : "unblockBooking",
				bookings:array_id.toString(),
				commerce:$("#booking-commerce").val()
				},
			success: function(respuesta) {
				alert(respuesta);
			}
		});
	}
	
	$(document).ready(function(){

		  var isDown = false;   // Tracks status of mouse button

			$(function() {
				$( document ).tooltip();
			});
  
		  $(document).mousedown(function() {
			isDown = true;      // When mouse goes down, set isDown to true
		  })
		  .mouseup(function() {
			isDown = false;    // When mouse goes up, set isDown to false
		  });

		  $(".clickableElement").live('mouseover', function() {
			if(isDown) {        // Only change css if mouse is down
			  selection($(this));
			}
		  });
		  
		  
		  $(".clickableElement").live('mousedown', function() {
			   selection($(this));
		  });
		   
		   
		function selection(obj){

			if(obj.hasClass("activeBook")){
			   obj.css({"border":"1px solid #FFFFFF"});
			   obj.removeClass("activeBook");
			}else{
			   obj.css({"border":"1px solid #FF0000"});
			   obj.addClass("activeBook"); 
			}   
		}
		$(function(){
		   var my_books = $('.activeBook'); 
		   $('#show_hide').click(function(){
			   my_books.each(function(){
				   var $this = $(this); 
				   if( $this.is(':visible') )
					   $this.hide(); 
				   else 
					   $this.show(); 
			   }); 
		   }); 
		}); 

		$('.booking-filter').on('change', function() {
			reloadCalendar();
		});
		
		$('.return-btn').on('click', function() {
			reloadCalendar();
		});
		
		function reloadCalendar(){
			$.ajax({
				type: 'GET',
				url: '<?=$formSaraDataURL?>',
				async: false,
				data: { 
					optionBooking : "reloadForm",
					month:$("#booking-month").val(),
					year:$("#booking-year").val(),
					company:$("#booking-company").val(),
					commerce:$("#booking-commerce").val()
					},
				success: function(respuesta) {
					$("#booking-calendar").html(respuesta);
					$(".btn-calendar").show();
					$(".return-btn").hide();
				}
			});
		}
	   
	});
</script>

<br/>
	<ul class="main-nav menu_calendar">
			<li class="btn-calendar">
				<a href="<?=$formSaraDataBookingList?>"><span>VER LISTADO</span></a>
			</li>
	</ul>
	<br/><br/><br/>
	
<div class="titulob">
	<div class="tituloimg">
		<h1>ADMINISTRAR DISPONIBILIDAD</h1>
	</div>
</div>

<div class="box box-color box-bordered no-seleccionable" >
	<div class="box-title">
		<h3>
			<select class="booking-filter" style="width:90px" id="booking-year"> 
				<option <?=($year=="2014")?"selected":""?>  VALUE="2014" >2014</option>
				<option <?=($year=="2015")?"selected":""?>  VALUE="2015" >2015</option>
			</select>
			
			<select class="booking-filter" id="booking-month">
				<option <?=($month=="1")?"selected":""?>  VALUE="1" >ENERO</option>
				<option <?=($month=="2")?"selected":""?>  VALUE="2" >FEBRERO</option>
				<option <?=($month=="3")?"selected":""?>  VALUE="3" >MARZO</option>
				<option <?=($month=="4")?"selected":""?>  VALUE="4" >ABRIL</option>
				<option <?=($month=="5")?"selected":""?>  VALUE="5" >MAYO</option>
				<option <?=($month=="6")?"selected":""?>  VALUE="6" >JUNIO</option>
				<option <?=($month=="7")?"selected":""?>  VALUE="7" >JULIO</option>
				<option <?=($month=="8")?"selected":""?>  VALUE="8" >AGOSTO</option>
				<option <?=($month=="9")?"selected":""?>  VALUE="9" >SEPTIEMBRE</option>
				<option <?=($month=="10")?"selected":""?>  VALUE="10" >OCTUBRE</option>
				<option <?=($month=="11")?"selected":""?>  VALUE="11" >NOVIEMBRE</option>
				<option <?=($month=="12")?"selected":""?>  VALUE="12" >DICIEMBRE</option>
			</select>
		</h3>
		<input type="hidden" id="booking-commerce"  value="<?=$this->commerce?>"/> 
		<ul class="main-nav menu_calendar">
			<li class="btn-calendar">
				<a onclick="showDetail()"><span>VER</span></a>
			</li>
			<li  class="btn-calendar">
				<a  onclick="block()" ><span>BLOQUEAR</span></a>
			</li>
			<li  class="btn-calendar">
				<a  onclick="unblock()" ><span>DESBLOQUEAR</span></a>
			</li>
			<li class="btn-calendar">
				<a  onclick="unselect()"><span>QUITAR SELECCION</span></a>
			</li>
			<li class="return-btn">
				<a  onclick="unselect()"><span>REGRESAR</span></a>
			</li>
		</ul>
	</div>		
	<div id="booking-calendar" class="box-content nopadding">		
		<?=$this->paintBookingForm($month,$year,$this->commerce)?>
	</div>
</div>