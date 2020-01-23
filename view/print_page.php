<?php 
	include('header.php');
	$count=0;


$rosterInfo = getLDAPstudentList($_GET['starnumber']);
if($rosterInfo["count"] > 0) 
{
$stud_rec = array();
for($i = 0; $i < $rosterInfo["count"]; $i++) 
{
$fn = $rosterInfo[$i]["givenname"][0];
$ln = $rosterInfo[$i]["sn"][0];
$student = array($ln.", ".$fn);
array_push($stud_rec, $student);
}
sort($stud_rec);
}
$i=0;
$arr1=array();
?>
    <!-- Page Content -->
  
    <div class="container">

        <div class="row">
			<div class="thumbnail">
				<div class= "text-over-image">
					<p>Center for Innovation in Teaching & Research</p>
				</div>
				
			</div>

			<div class="well">
				<h2 align="center" >Attendance Sign-in Sheet<br>
						<?php echo $title; ?>
						</h2>
					
				  
			
						<label for = "from-input">Date :  </label><?php echo date('Y-m-d'); ?>
					<img class = "print-sheet" src = "https://www.wiu.edu/citr/AttendanceResponsive/images/Devices-printer-icon.png" onclick="window.print(); return false;"><br>
						
						<form id = "attendance_quick_form"  method = "post" action = ""  style="page-break-before: avoid">
						<div class="table-responsive"> 
						<table id = "attendance_print_table">   

					<th style="padding: 5px">Student Name</th>
								<th style="padding: 5px">Signature</th>
								<th style="padding: 5px">Left Early</th>
							</thead>
							<tbody> 
							<?php
								
							
							//sort($stud_rec_array);
							//echo count($stud_rec);
							$arr=array_slice($stud_rec,0,26);
							
							$arr1=array_slice($stud_rec,26,26);
							$arr2=array_slice($stud_rec,52);
							//print_r($arr2);

							//print_r($arr1);
								foreach($arr as $values)
								{
									$studNameArr= explode(",", $values[0]);
									//echo "NIKKI".$studNameArr;
									$studName1 = $studNameArr[1];	
							?>
							
							<tr>
										<td style="padding: 5px ">
											<?php echo $values[0]; 
												$i++; ?>
										</td>
										<td style="padding: 5px" width="30%">
										</td>	
										<td style="padding: 5px" >
											<input id = "stud_le_<?php echo $studName1; ?>" value="LeftEarly" type="checkbox" />
										</td>
									</tr>
									
									<?php }
									?>
									</tbody>
									</table>

							<input type="hidden" name="star" id="star" value="<?php echo $star_number; ?> ">
					<input type="hidden" name="stuList" id="stuList" value="<?php echo $stuList; ?> ">
							</form>


								<?php if(count($stud_rec)>26) { ?>

							<form id = "attendance_quick_form2"  method = "post" action = " "  style="page-break-inside: avoid">
								<div class="table-responsive"> 
								<table id = "attendance_print_table">  
								<th style="padding: 5px">Student Name</th>
								<th style="padding: 5px">Signature</th>
								<th style="padding: 5px">Left Early</th>
							</thead>
								<tbody>
									
								<?php	foreach($arr1 as $values)
								{
									$studNameArr= explode(",", $values[0]);
									//echo "NIKKI".$studNameArr;
									$studName1 = $studNameArr[1];	
							?>
							
							<tr>
										<td style="padding: 5px ">
											<?php echo $values[0]; 
												$i++; ?>
										</td>
										<td style="padding: 5px" width="30%">
										</td>	
										<td style="padding: 5px" >
											<input id = "stud_le_<?php echo $studName1; ?>" value="LeftEarly" type="checkbox" />
										</td>
									</tr>
									
									<?php }
									?>
									
									</tbody>
									</table>		
					<input type="hidden" name="star" id="star" value="<?php echo $star_number; ?> ">
					<input type="hidden" name="stuList" id="stuList" value="<?php echo $stuList; ?> ">
					
								
		</form>

<?php } ?>


						<?php if(count($stud_rec)>52)  { ?>


						<form id = "attendance_quick_form3"  method = "post" action = "" style="page-break-inside: avoid">
								<div class="table-responsive"> 
								<table id = "attendance_print_table">  
								<tbody>
									<th style="padding: 5px">Student Name</th>
								<th style="padding: 5px">Signature</th>
								<th style="padding: 5px">Left Early</th>
							</thead>
									
								<?php	foreach($arr2 as $values)
								{
									$studNameArr= explode(",", $values[0]);
									//echo "NIKKI".$studNameArr;
									$studName1 = $studNameArr[1];	
							?>
							
							<tr>
										<td style="padding: 5px ">
											<?php echo $values[0]; 
												$i++; ?>
										</td>
										<td style="padding: 5px" width="30%">
										</td>	
										<td style="padding: 5px" >
											<input id = "stud_le_<?php echo $studName1; ?>" value="LeftEarly" type="checkbox" />
										</td>
									</tr>
									
									<?php }
									?>
									
									</tbody>
									</table>		
					<input type="hidden" name="star" id="star" value="<?php echo $star_number; ?> ">
					<input type="hidden" name="stuList" id="stuList" value="<?php echo $stuList; ?> ">						
		</form>
		<?php } ?>
		</div>		
			<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a>
		</div>
			</div>
	<!-- /.container -->

<?php 
	include('footer.php');
?>