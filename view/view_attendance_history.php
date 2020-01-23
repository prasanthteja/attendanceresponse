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
					<h4>Attendance History</h4>
				</div>
			</div>

			<div class="well">
				
				<?php
					//echo $attendanceHistory[0][5];
					for($i = 0; $i < count($attendanceHistory); $i++)
					{
						$term = $attendanceHistory[$i][4];
						// Add space in term if it doesn't has
						if(strrpos($attendanceHistory[$i][4], " ") === false)
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
				?>
						<div class="row attendance-history-odd-even">
							<div class="col-md-12">	
								<span class="pull-left margin-bottom-10"><?php echo $attendanceHistory[$i][2] . " (" . $attendanceHistory[$i][1] .") | Section: " . $attendanceHistory[$i][3]; ?></span>
								<div class="course-info">
									<div class = "course-details">
										<p class="clear-both"><?php echo "STAR #: " . $attendanceHistory[$i][0]; ?></p>
										<p class="clear-both"><?php echo "Term: " . $term; ?></p>
										<a class = "btn btn-success" href="https://www.wiu.edu/citr/AttendanceResponsive/index.sphp<?php echo $attendanceHistory[$i][5]; ?>">Details</a>
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
