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
					<h4>User Preference</h4>
				</div>
			</div>
			<div class = "well">
				<div class="row">
					<div class="col-md-12">	
						<p class = "preference_title"> Set User Preference <p>
						<Label>Select the default/initial value of the attendance using the option below.</label><br>
					</div>
				</div>
			</div>
			<form id = "attendance_quick_form" method = "post" action = "/citr/AttendanceResponsive/index.sphp?action=setPreference">
				<div class = "well">
					<div class="row">
						<div class="col-md-12">	
							<p class = "preference_title"> Set Attendance Preference <p>
							<Label>Preference:</label><br>
							<?php
								$present_checked = $absent_checked = $excused_checked = $tardy_checked = "";
								if($preferenceold === "absent")
								{
									$absent_checked = "selected";
								}
								if($preferenceold === "present")
								{
									$present_checked = "selected";
								}
								if($preferenceold === "excused")
								{
									$excused_checked = "selected";
								}
								if($preferenceold === "tardy")
								{
									$tardy_checked = "selected";
								}
							?>
							<select name="preference" id="preference">
								<option>Select One</option>
								<option <?php echo $absent_checked; ?>  value="absent">Absent</option>
								<option <?php echo $present_checked; ?>  value="present">Present</option>
								<option <?php echo $excused_checked; ?>  value="tardy">Tardy</option>
								<option <?php echo $tardy_checked; ?>  value="excused">Excused</option>
							</select>
						</div>
					</div>
				</div>
				<div class = "well">
					<div class="row">
						<div class="col-md-12">	
							<p class = "preference_title"> Excessive Absence Notification <p>
							<Label>Select # of absences you consider excessive:</label><br>
							<select name = "preference2" id = "preference2">
								<option>Select one</option>
								<?php
									
									for ($i = 10; $i >= 1; $i--)
									{
									
									
										if($absenceLimitold == $i)
										{
								?>
											<option selected value = "<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php
										}
										else
										{
								?>
											<option value = "<?php echo $i; ?>"><?php echo $i; ?></option>
								<?php
										}
									}
								?>
							</select>
							<div class="label_checkbox">
								<?php
									if($studentmail12 == 1)
									{
								?>
										<input id = "stud_email" name = "required[]" checked = "checked" value="stumail" type="checkbox" />
										<label for = "stud_email" class = "set_pref_width">
											Student Email
										</label>
								<?php
									}
									else
									{
								?>
										<input id = "stud_email" name = "required[]" value="stumail" type="checkbox" />
										<label for = "stud_email" class = "set_pref_width">
											Student Email
										</label>
								<?php
									}
								?>
							</div>
							<div class="label_checkbox">
								<?php
									if($instructormail12 == 1)
									{
								?>
										<input id = "inst_email" name = "required[]" checked = "checked" value="instmail" type="checkbox" />
										<label for = "inst_email" class = "set_pref_width">
											Instructor Email
										</label>
								<?php
									}
									else
									{
								?>
										<input id = "inst_email" name = "required[]" value="instmail" type="checkbox" />
										<label for = "inst_email" class = "set_pref_width">
											Instructor Email
										</label>
								<?php
									}
								?>
							</div>
							<div class="label_checkbox">
								<?php
									if($advisormail12 == 1)
									{
								?>
										<input id = "adv_email" name = "required[]" checked = "checked" value="advmail" type="checkbox" />
										<label for = "adv_email" class = "set_pref_width">
											Advisor Email
										</label>
								<?php
									}
									else
									{
								?>
										<input id = "adv_email" name = "required[]" value="advmail" type="checkbox" />
										<label for = "adv_email" class = "set_pref_width">
											Advisor Email
										</label>
								<?php
									}
								?>
							</div>
							<div class="label_checkbox">
								<?php
									if($beforeabsenselimit == 1)
									{
								?>
									<input id = "warn_msg" name = "required[]" checked = "checked" value="beforeabsenselimit" type="checkbox" />
									<label for = "warn_msg" class = "set_pref_width">
										Send a warning message to student and instructor once before hitting denoted limit
									</label>
								<?php
									}
									else
									{
								?>
										<input id = "warn_msg" name = "required[]" value="beforeabsenselimit" type="checkbox" />
										<label for = "warn_msg" class = "set_pref_width">
											Send a warning message to student and instructor once before hitting denoted limit
										</label>
								<?php
									}
								?>		
							</div>
							<div class="label_checkbox">
								<?php
									if($afterabsenselimit == 1)
									{
								?>
										<input id = "abs_limit_email" name = "required[]" checked = "checked" value="afterabsenselimit" type="checkbox" />
										<label for = "abs_limit_email" class = "set_pref_width">
											Mail for each absent after absenceLimit
										</label>
								<?php
									}
									else
									{
								?>
										<input id = "abs_limit_email" name = "required[]" value="afterabsenselimit" type="checkbox" />
										<label for = "abs_limit_email" class = "set_pref_width">
											Mail for each absent after absenceLimit
										</label>
								<?php
									}
								?>	
							</div>
							<div class="label_checkbox">
								<?php
									if($eachabsentValue == 1)
									{
								?>
										<input id = "abs_email" name = "required[]" checked = "checked" value="emaileachabsent" type="checkbox" />
										<label for = "abs_email" class = "set_pref_width">
											Email student with each absent
										</label>
								<?php
									}
									else
									{
								?>
										<input id = "abs_email" name = "required[]" value="emaileachabsent" type="checkbox" />
										<label for = "abs_email" class = "set_pref_width">
											Email student with each absent
										</label>
								<?php
									}
								?>
							</div>
						</div>
					</div>
				</div>
				<div class = "well">
					<div class="row">
						<div class="col-md-12">	
							<p class = "preference_title"> Email Changes in Attendance Preference <p>
							<div class="label_checkbox">
								<?php
									if($emailmechangesquery == 1)
									{
								?>
										<input id = "email_changes" name = "required[]" checked = "checked" value="emailchanges0" type="checkbox" />
										<label for = "email_changes" class = "set_pref_width">
											Email me changes
										</label>
								<?php
									}
									else
									{
								?>
										<input id = "email_changes" name = "required[]" value="emailchanges0" type="checkbox" />
										<label for = "email_changes" class = "set_pref_width">
											Email me changes
										</label>
								<?php
									}
								?>
							</div>
						</div>
					</div>
				</div>
				<div class = "well">
					<div class="row">
						<div class="col-md-12">	
							<p class = "preference_title"> Share Attendance with Chair/Director <p>
							<div class="label_checkbox">
								<input id = "chairshare" name = "chairshare" value="chairshare1" checked = "checked" type="checkbox" />
								<label for = "chairshare" class = "set_pref_width">
									Share the attendance with the chair
								</label>
							</div>
						</div>
					</div>
				</div>
				<div class = "well">
					<div class="row">
						<div class="col-md-12">	
							<p class = "preference_title"> Share Attendance with Advisor <p>
							<div class="label_checkbox">
								<input id = "advisorshare" name = "advisorshare" value="advisorshare1" checked = "checked" type="checkbox" />
								<label for = "advisorshare" class = "set_pref_width">
									Share the attendance with the Advisor
								</label>
							</div>
						</div>
					</div>
					<input type="hidden" name="star" id="star" value="<?php echo $_GET['starnumber']; ?>">
					<input type="submit" name="Submit" id="Submit" value="Set Preference" class="btn btn-success"/>
					<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a>
				</div>
			</form>
	<!-- /.container -->

<?php 
	include('footer.php');
?>