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
	<script src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
	<script src="https://cdn.datatables.net/fixedheader/3.1.3/js/dataTables.fixedHeader.min.js"></script>
	<script src="js/voice.js"></script>
	
	<script type="text/javascript">
		$(document).ready(function() {
			$("#date, #note_date").datepicker({
				dateFormat: "yy-mm-dd"
			}).datepicker("setDate", "0");
			
			$('#attendance_quick_table').DataTable({
				"fixedHeader": {
					header: true
				},
				"aoColumnDefs": [
				  { "bSortable": false, "aTargets": [ 1 ] },
				  { "bSortable": false, "aTargets": [ 2 ] },
				  { "bSortable": false, "aTargets": [ 3 ] },
				  { "bSortable": false, "aTargets": [ 4 ] },
				  { "bSortable": false, "aTargets": [ 5 ] },
				],
				"lengthMenu": [[150], []] //Changed to 150 from 50 by hari. to make Max full rows fit
			});
			$('#note_table').DataTable({
				"lengthMenu": [[150], []] //Changed to 150 from 50 by hari. to make Max full rows fit
			});
			$('#attendance_class_table').DataTable({
				"aaSorting": [],
				"aoColumnDefs": [
				  { "bSortable": false, "aTargets": [ 0 ] },
				  { "bSortable": false, "aTargets": [ 1 ] },
				  { "bSortable": false, "aTargets": [ 2 ] },
				  { "bSortable": false, "aTargets": [ 3 ] },
				  { "bSortable": false, "aTargets": [ 4 ] },
				  { "bSortable": false, "aTargets": [ 5 ] },
				  { "bSortable": false, "aTargets": [ 6 ] },
				],
				"lengthMenu": [[150], []] //Changed to 150 from 50 by hari. to make Max full rows fit
			});
			
			$('#attendance_quick_table_length, #attendance_quick_table_paginate').remove();
			$('#note_table_length, #note_table_paginate').remove();
			$('#attendance_class_table_length, #attendance_class_table_paginate').remove();
			
			$('#attendance_stud_table').DataTable({
				"lengthMenu": [[150], []] //Changed to 150 from 50 by hari. to make Max full rows fit
			});
			$('#attendance_stud_table_length, #attendance_stud_table_paginate').remove();
			
			if( $( window ).width() < 668) 
			{
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