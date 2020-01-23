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
					<h4>Edit Attendance</h4>
				</div>
			</div>

			<div class="well padding_2">
				<?php
					if($operation === "update")
					{
				?>
						<input type="submit" name="Submit" id="Submit" value="Save" class="btn btn-success"/>
						<input type="submit" name="Submit" id="Submit" value="Save and Enter Another" class="btn btn-success"/>
				<?php
					}
					if ($operation === "edit")
					{
				?>
					<input type="submit" name="Submit" id="Submit" value="Save Changes" class="btn btn-success"/>
					<input type="submit" name="Submit" id="Submit" value="Remove All Records for This Day" class="btn btn-success"/>
				<?php
					}
				?>
				<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a>
				<form id = "attendance_quick_form" method = "post" action = "/citr/AttendanceResponsive/index.sphp">
					<label for = "from-input">Date : </label>
					<?php 
						if($operation == "edit")
						{
					?>
							<input type = "text" id = "edit_attendance_date" name = "date" value = "<?php echo $attendance_date; ?>">
					<?php
						}
						else
						{
					?>
							<input type = "text" id = "date"  name = "date" placeholder = "Click here to select dates">
					<?php
						}
					?>
				
					<div class="table-responsive">          
						<table id = "attendance_quick_table" style = "width: auto;" >
							<thead>
								<th id = "stud_name" >Student Name</th>
								<th id = "stud_pr" >Attendance Status</th>
								<th id = "stud_le" >Left Early</th>
							</thead>
							<tbody>
							<?php
								$i = 1;
								foreach($stud_rec_array as $values)
								{
									$getPresentCount = $values[7];
									$getAbsentCount = $values[8];
									$getTardyCount = $values[9];
									$getExcusedCount = $values[10];
									$getLeftEarlyCount = $values[11];
									$total = $getPresentCount + $getAbsentCount + $getTardyCount + $getExcusedCount;
									$present = ( $getPresentCount - $getLeftEarlyCount )*100/$total;
									$absent = $getAbsentCount*100/$total;
									$tardy = $getTardyCount/$total*100;
									$excused = $getExcusedCount/$total*100;
									$left = $getLeftEarlyCount/$total*100;
									
									if( $values[13] == "Y" )
									{
										$left_early = "checked";
									}
									
									if ( $values[13] == "N" )
									{
										$left_early = "";
									}
									$present_checked = $absent_checked = $excused_checked = $tardy_checked = "";
									if($values[12] == "present")
										$present_checked = "selected";
									
									if($values[12] == "absent")
										$absent_checked = "selected";
									
									if($values[12] == "excused")
										$excused_checked = "selected";
									
									if($values[12] == "tardy")
										$tardy_checked = "selected";
									
									$studNameArray = explode(",", $values[0]);
									$studName = $studNameArray[0];
							?>
									<tr>
										<td>
											<div id = "pie" class = "multbar_full_attendance">
												<img class = "stud_img" src="watermark.sphp?filename=<?php echo $values[5]; ?>">
												<div class = "details">
													<span>
													<?php 
														echo $values[0];
													?>
													</span>
													<div class = "icons_wrap">
														<i class="fa fa-2x fa-volume-up" aria-hidden="true" onclick="responsiveVoice.speak('<?php echo $values[0]; ?>','US English Female');"></i>
														<a href = "https://www.wiu.edu/citr/resources/students/index.sphp?uid=<?php echo $values[14]; ?>&starnumber=<?php echo $_GET['starnumber'];?>" target = "_blank" ><i class="fa fa-2x fa-comment" aria-hidden="true"></i></a>
														<a class = "email_icon" href="<?php echo $values[6]; ?>" target = "_blank"><i class="fa fa-2x fa-envelope" aria-hidden="true"></i></a>
														<a href="<?php echo $values[2]; ?>" target = "_blank"><img height = "28" width = "28" src="/citr/AttendanceResponsive/images/refer.png" /></a>
													
													<?php
														if(isset($values[3]))
														{
													?>
															 <a href="<?php echo $values[1]; ?>" target = "_blank"><img height = "28" width = "28" src="/citr/AttendanceResponsive/images/notepad.png" /><?php echo $values[3]; ?></a>
													<?php 
														}
													?>
													</div>
													<?php
														if($present > 85) 
														{
													?>
															<div class = "attend_details">
																<span><strong>Present: <span style = "background-color: green; color: white; border-radius: 5px; padding: 5px;"><?php echo round($present); ?>%</span></strong></span>
															</div>
													<?php
														}
													?>
													<?php
														if($present < 84.9 && $present > 70) 
														{
													?>
															<div class = "attend_details">
																<span><strong>Present: <span style = "background-color: yellow; color: black; border-radius: 5px; padding: 5px;" ><?php echo round($present); ?>%</span></strong></span>
															</div>
													<?php
														}
													?>
													<?php
														if($present < 70) 
														{
													?>
															<div class = "attend_details">
																<span><strong>Present: <span style = "background-color: red; color: white; border-radius: 5px; padding: 5px;" ><?php echo round($present); ?>%</span></strong></span>
															</div>
													<?php
														}
													?>
												</div>
											</div>
										</td>
										<td>
											<select id = "stud_pr_<?php echo $studName; ?>" name = "attendance_status" class="textInput">
												<option <?php echo $present_checked; ?> value="present" >Present</option>
												<option <?php echo $absent_checked; ?> value = "absent"  >Absent</option>
												<option <?php echo $excused_checked; ?> value = "excused"  >Excused</option>
												<option <?php echo $tardy_checked; ?> value = "tardy"  >Tardy</option>
											</select>
										</td>
										<td><input id = "stud_le_<?php echo $studName; ?>" value="LeftEarly" type="checkbox" <?php echo $left_early; ?> ><label for = "stud_le_<?php echo $studName; ?>" ></label></td>
									</tr>
							<?php
									$i++;
								}
							?>
							</tbody>
						</table>
					</div>
					<input type="hidden" name="star" id="star" value="<?php echo $star_number; ?>">
					<input type="hidden" name="stuList" id="stuList" value="<?php echo $stuList; ?>">
					<?php
						if($operation === "update")
						{
					?>
							<input type="submit" name="Submit" id="Submit" value="Save" class="btn btn-success"/>
							<input type="submit" name="Submit" id="Submit" value="Save and Enter Another" class="btn btn-success"/>
					<?php
						}
						if ($operation === "edit")
						{
					?>
						<input type="submit" name="Submit" id="Submit" value="Save Changes" class="btn btn-success"/>
						<input type="submit" name="Submit" id="Submit" value="Remove All Records for This Day" class="btn btn-success"/>
					<?php
						}
					?>
					<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a>
				</form>
		</div>
	<!-- /.container -->

<?php 
	include('footer.php');
?>