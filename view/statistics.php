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
					<h4>Class Summary</h4>
				</div>
			</div>
			<div class="well">
				<form id = "attendance_quick_form" method = "post" action = "/citr/AttendanceResponsive/index.sphp">
					<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=attendanceHistory" class = "btn btn-success">List Attendance History</a>
					<div class="table-responsive">          
						<table id = "attendance_class_table" class="table">
							<thead>
								<th id = "stud_name" >Student Name</th>
								<th id = "stud_pr" >Present</th>
								<th id = "stud_ab" >Absent</th>
								<th id = "stud_ea" >Excused Absence</th>
								<th id = "stud_tr" >Tardy</th>
								<th id = "stud_le" >Left Early</th>
								<th id = "stud_le" >Total</th>
							</thead>
							<tbody>
							<?php
								foreach($studentArray as $values)
								{
									if($values[7] < $totalNumber)
									{
										$spe = "<span class = 'red'> * </span>";
							?>
										<tr>
											<td><a href="?action=studentInfo&id=<?php echo $values[11]; ?>&starnumber=<?php echo $values[10]; ?>"><?php echo $spe . " " . $values[0]; ?></a></td>
									<?php 
									}
									else
									{
									?>
										<tr>
											<td><a href="?action=studentInfo&id=<?php echo $values[11]; ?>&starnumber=<?php echo $values[10]; ?>"><?php echo $values[0]; ?></a></td>
									<?php
									}
									?>
											<td><?php echo $values[2]; ?></td>
											<td><?php echo $values[3]; ?></td>
											<td><?php echo $values[4]; ?></td>
											<td><?php echo $values[5]; ?></td>
											<td><?php echo $values[6]; ?></td>
											<td><?php echo $values[7]; ?></td>
										</tr>
							<?php
								}
							?>
								<tr>
									<td><strong>Total</strong></td>
									<td><strong><?php echo $totalPresentcount; ?></strong></td>
									<td><strong><?php echo $totalAbsent; ?></strong></td>
									<td><strong><?php echo $totalExcused; ?></strong></td>
									<td><strong><?php echo $totalTardy; ?></strong></td>
									<td><strong><?php echo $totalLeft; ?></strong></td>
									<td></td>
								</tr>
							</tbody>
						</table>
					</div>
					<div class="row attendance-history-odd-even" style = "margin-left: 0px;">
						<div class="col-md-12">	
							<span class="pull-left margin-bottom-10"><span class = "red">*</span> Attendance for this student does not meet the total number of days attendance was tracked. This could be the result of the student enrolling in the course late in the term, or having dropped the course prior to the end of the term.</span>
						</div>
					</div>
					<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a>
				</form>
				
		</div>
	<!-- /.container -->

<?php 
	include('footer.php');
?>