<?php 
	include('header.php');
	$star_number = $_GET['starnumber'];
?>
    <!-- Page Content -->
    <div class="container">

        <div class="row">
			<div class="thumbnail">
				<div class= "text-over-image">
					<p>Center for Innovation in Teaching & Research</p>
				</div>
				<div class="caption-full">
					<h4>Edit Attendance</h4>
				</div>
			</div>

			<div class="well">
				<form id = "attendance_quick_form" method = "post" action = "/citr/AttendanceResponsive/index.sphp?action=selectCourse&starnumber=<?php echo $star_number; ?>&operation=edit">
					<label for = "from-input">Date : </label>
					<select id="future_date" name = "future_date" class="textInput">
						<option>Select One</option>
					<?php	
						foreach($attendance_date_array as $date)
						{
					?>	
							<option selected=""><?php echo $date; ?></option>
					<?php
						}
					?>
					</select>
					<br><br>
					<input class = "btn btn-success" type="submit" name="submit" value="Submit">
					<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a>
				</form>
		</div>
	<!-- /.container -->

<?php 
	include('footer.php');
?>