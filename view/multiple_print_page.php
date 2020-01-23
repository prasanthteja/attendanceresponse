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
					<h4>Attendance Sign-in Sheets<br>
						<?php echo $title; ?>
					</h4>
				</div>
			</div>
			<div class="well">
				<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a>	
				<form id = "attendance_multiprint_form" method = "post" action = "">
					<div class = "inst_multiprint">
						<p> Instructions to print multiple sign-in sheets: </p>
						<ul>
							<li>Click on date text field to select dates.</li>
							<li>Click on the <strong>Build Sheets</strong> to generate attendance sheets.</li>
							<li>Click on the <strong>Print Icon </strong> to print the attendance sheets.</li>
						</ul>
						<p> Note: By default current date will be selected. </p>
						<label for = "from-input" id = "multi_date_label" >Date :  </label>
						<input type = "text" id = "multi_date" class = "margin-bottom-10" name = "multi_date" placeholder = "Click here to select dates">
						<input type="submit" name="Submit" id="Submit" value="Build Sheet" class="btn btn-success"/>
						<img class = "print-sheet" src = "https://www.wiu.edu/citr/AttendanceResponsive/images/Devices-printer-icon.png" onclick="window.print(); return false;"><br>
					</div>
					<?php
					if(isset($_POST) && $_POST['multi_date'] != "")
					{
						$date_array = explode(", ", $_POST['multi_date'] );
						$display_array = [];
						foreach($date_array as $key => $date_value)
						{ 
					?>
							<div class="caption-full-print">
								<h4><?php echo $title; ?></h4>
							</div>
							<label for = "from-input">Date : <?php echo $date_value; ?> </label>
							<div class="table-responsive">          
								<table id = "attendance_print_table" class="table">
									<thead>
										<th>Student Name</th>
										<th>Signature</th>
										<th>Left Early</th>
									</thead>
									<tbody>
					<?php
										foreach($stud_rec_array as $values)
										{
											$studNameArray = explode(",", $values[0]);
											$studName = $studNameArray[0];
				?>
											<tr>
												<td><?php echo $values[0]; ?></td>
												<td>
												
												</td>	
												<td><input id = "stud_le_<?php echo $studName; ?>" value="LeftEarly" type="checkbox" /></td>
											</tr>
					<?php
										}
					?>

									</tbody>
								</table>
							</div>
					<?php
						}
					}
					else
					{
					?>	
						<div class="caption-full-print">
								<h4><?php echo $title; ?></h4>
						</div>
						<label for = "from-input">Date : <?php echo date('Y-m-d'); ?> </label>
						<div class="table-responsive">          
							<table id = "attendance_print_table" class="table">
								<thead>
									<th>Student Name</th>
									<th>Signature</th>
									<th>Left Early</th>
								</thead>
								<tbody>
					<?php
									sort($stud_rec_array);
									foreach($stud_rec_array as $values)
									{
										$studNameArray = explode(",", $values[0]);
										$studName = $studNameArray[0];
					?>
										<tr>
											<td>
												<?php echo $values[0]; ?>
											</td>
											<td>
											</td>	
											<td>
												<input id = "stud_le_<?php echo $studName; ?>" value="LeftEarly" type="checkbox" />
											</td>
										</tr>
					<?php
									}
					?>
								</tbody>
							</table>
						</div>
					<?php	
					}
					?>
				</form>
			</div>
	<!-- /.container -->

<?php 
	include('custome_footer.php');
?>