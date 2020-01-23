	<!-- Footer -->
	<footer>
		<div class="row footer-content">
			<div class="col-lg-7 footer-margin">
				<p>Center for Innovation in Teaching & Research 2017</p>
				<p>	CITR | Phone: (309) 298-2434 | Email: CITR@wiu.edu</p>
				<p> 2017 by CITR. All rights reserved.</p>
			</div>
			<div class="col-lg-5">
				<div id="contact">
					<div id="footerlogo">
					<img src="https://www.wiu.edu/citr/AttendanceResponsive/images/wiulogo_black.png" alt="Logo image of Western Illinois University bell tower"></div>
				</div>
			</div>
		</div>
	</footer>
	</div>
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<script src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
	<script src="multidatepicker/js/jquery-ui.multidatespicker.js"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$("#date, #note_date").datepicker({
				dateFormat: "yy-mm-dd"
			}).datepicker("setDate", "0");
			
			$("#multi_date").multiDatesPicker({
				dateFormat: "yy-mm-dd"
			});
			
			$('#attendance_quick_table').DataTable({
				"aoColumnDefs": [
				  { "bSortable": false, "aTargets": [ 1 ] },
				  { "bSortable": false, "aTargets": [ 2 ] },
				  { "bSortable": false, "aTargets": [ 3 ] },
				  { "bSortable": false, "aTargets": [ 4 ] },
				  { "bSortable": false, "aTargets": [ 5 ] },
				]
			});
			$('#note_table').DataTable();
			
			$('#attendance_quick_table_length, #attendance_quick_table_paginate').remove();
			$('#note_table_length, #note_table_paginate').remove();
			if( $( window ).width() < 668) 
			{
				console.log("Test");
				$('#stud_name').text("SN");
				$('#stud_pr').text("PR");
				$('#stud_ab').text("AB");
				$('#stud_ea').text("EA");
				$('#stud_tr').text("TR");
				$('#stud_le').text("LE");
			}
			$(window).scroll(function() {
				if ( $(window).scrollTop() > amountScrolled ) {
					$("a.back-to-top").fadeIn("slow");
				} else {
					$("a.back-to-top").fadeOut("slow");
				}
			});
			var amountScrolled = 300;

			$("a.back-to-top, a.simple-back-to-top").click(function() {
				$("html, body").animate({
					scrollTop: 0
				}, 700);
				return false;
			});
		});
	</script>
</body>

</html>