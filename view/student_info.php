<?php 
	include('header.php');
?>
    <!-- Page Content -->
    <div class="container">

        <div class="row">
			<div class="thumbnail">
				<div class= "text-over-image">
					<p>Center for Innovation in Teaching & Research</p>
				</div>
				<div class="caption-full">
					<h4>Student Information</h4>
				</div>
			</div>
			<div class="well">
				<div class = "row attendance-history-odd-even">
					<div class = "col-md-12">
						<div class = "col-md-2">
							<img class = "stud_img" src="https://www.wiu.edu/citr/AttendanceTracking/picture/<?php echo $info; ?>"><br>
						</div>
						<div class = "col-md-10"> 
							<p> First Name: <?php echo $firstName; ?></p>
							<p> Last Name: <?php echo $lastName; ?></p>
							<p> Call Name: <?php echo $name; ?></p>
							<p> Email: <?php echo $mail; ?></p>
							<p> Major: <?php echo $studentInfo[0]["wiumajor"][0]; ?></p>
							<p> Minor: <?php echo $studentInfo[0]["wiuminor"][0]; ?></p>
							<p> Advisor: <a href="mailto:<?php echo $advisormail; ?>" style="target-new: tab;"><?php echo $advisorName; ?></a></p>
							<p> Title: <?php echo $title; ?></p>
							<p> Department: <?php echo $dept; ?></p>
						</div>
					</div>
				</div>
				<div class = "row">
					<div class = "col-md-12">
						<label for = "from-input" id = "multi_date_label" >Attendance Summary :  </label>
						<ul>
							<li>Present: <?php echo $getCount[0]; ?></li>
							<li>Absent: <?php echo $getCount[1]; ?></li>
							<li>Excused: <?php echo $getCount[2]; ?></li>
							<li>Tardy: <?php echo $getCount[3]; ?></li>
							<li>Left Early: <?php echo $getLeftEarlyCount; ?></li>
						</ul>
						<div class="table-responsive width-40">          
							<table id = "attendance_stud_table" class="table">
								<thead>
									<th>Date</th>
									<th>Description</th>
								</thead>
								<tbody>
								<?php
									foreach( $row as $values )
									{
								?>
										<tr>
											<td style = "text-align: center;"><?php echo $values[0]; ?></td>
											<td><?php echo $values[1]; ?></td>
										</tr>
								<?php
									}
								?>
								</tbody>
							</table>
						</div>
						<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a>
					</div>
				</div>
	<!-- /.container -->

<?php 
	include('footer.php');
?>