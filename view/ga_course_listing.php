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
					<h4>Attendance Tracking</h4>
					<p>WIU abides by the Family Educational Rights and Privacy Act of 1974, and takes precautions to prevent the disclosure of confidential information. Use of this system constitutes consent to these terms.</p>
					<p>Contact CITR at citr@wiu.edu or 309/298-2434.</p>
				</div>
			</div>

			<div class="well">
				
				<?php
					for($i = 0; $i < count($final_course_details); $i++)
					{
						$term = $final_course_details[$i][9];
						// Add space in term if it doesn't has
						if(strrpos($final_course_details[$i][9], " ") === false)
						{
							if( preg_match( "/Fall/", $term ) )
							{
								$term = wordwrap( $term, 4, " ", true );
							}
							else if( preg_match( "/Spring/", $term ) )
							{
								$term = wordwrap( $attendanceHistory[$i][4], 6, " ", true );
							}
						}
						else
						{
							$term = $attendanceHistory[$i][4];
						}
						$termYear = substr($term, -4);
				?>
					<div class="row">
						<div class="col-md-12">	
							<span class="pull-left"><?php echo $final_course_details[$i][4] . " (" . $final_course_details[$i][0] .") | Section: " . $final_course_details[$i][3]; ?></span>
							<div class="course-info">
								<div class = "course-details">
									<p class="clear-both"><?php echo "STAR #: " . $final_course_details[$i][2]; ?></p>
									<p class="clear-both"><?php echo "Enrollment: " . $final_course_details[$i][1]; ?></p>
									<p class="clear-both"><?php echo "Meets: " . $final_course_details[$i][5] . " from " .$final_course_details[$i][6]; ?></p>
									<p class="clear-both"><?php echo "Term: " . $term; ?></p>
									<a href="https://www.wiu.edu/citr/resources/students/?action=roster&id=<?php echo $final_course_details[$i][2]; ?>" target = "_blank">View/Download a Roster or Email/Text Students/ Manage RSS Feed/ Event Mailer</a>
								</div>
								<div class = "course-link">
									<a class="btn btn-success" href= "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=chooseCourseQuick&starnumber=<?php echo $final_course_details[$i][2]; ?>&operation=update" >Quick Attendance</a>
									<a class="btn btn-success" href= "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=selectCourse&starnumber=<?php echo $final_course_details[$i][2]; ?>&operation=update" >Full Featured Attendance</a>
									<a class="btn btn-success" href= "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=editAttendance&starnumber=<?php echo $final_course_details[$i][2]; ?>" >Edit/Update</a>
									<a class="btn btn-success" href= "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=attendancePreference&starnumber=<?php echo $final_course_details[$i][2]; ?>" >Preference</a>
									<a class="btn btn-success" href= "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=exportAttendance&starnumber=<?php echo $final_course_details[$i][2]; ?>">Export Attendance</a>
									<a class="btn btn-success" href= "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=viewClassSummary&termYear=<?php echo $termYear; ?>&subjectId=<?php echo $final_course_details[$i][2]; ?>" >View Class Summary</a>
									<a class="btn btn-success" href= "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=emailStudentReport&starnumber=<?php echo $final_course_details[$i][2]; ?>">Email Student Reports</a>
									<a class="btn btn-success" href= "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=printPreview&starnumber=<?php echo $final_course_details[$i][2]; ?>" >Print a Sign-in Sheet</a>
									<a class="btn btn-success" href= "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=printMultiplePreview&starnumber=<?php echo $final_course_details[$i][2]; ?>" >Print Multiple Sign-in Sheet</a>
								</div>
							</div>
						</div>
					</div>
					<hr>
				<?php	
					}
				?>
			</div>
		</div>
	<!-- /.container -->

<?php 
	include('footer.php');
?>   
