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
					<h4>Advisor Details</h4>
				</div>
			</div>
			<?php
				if($advDetails['count'] > 0)
				{
			?>
					<div class="well">
						<div class="row">
							<div class="col-md-12">	
								<span class="pull-left">Advisor details for student name: <?php echo $studentName; ?></span>
								<div class="course-info">
									<div class="course-details">
										<p class="clear-both"> Advisor Name: <?php echo $advName; ?></p>
										<p class="clear-both">Advisor E-mail: <?php echo $advEmail; ?></p>
										<p class="clear-both">Advisor Phone Number : <?php echo $advContactNumber; ?></p>
										<p>To goto course list <a href="https://www.wiu.edu/citr/AttendanceResponsive/index.sphp">Click here</a></p>
									</div>
								</div>
							</div>
						</div>
					</div>
			<?php
				}
				else
				{
			?>
					<div class="well">
						<div class="row">
							<div class="col-md-12">	
								<span class="pull-left">No advisor found for student name: <?php echo $studentName; ?></span>
								<div class="course-details">
									<p class="clear-both">To goto course list <a href="https://www.wiu.edu/citr/AttendanceResponsive/index.sphp">Click here</a></p>
								</div>
							</div>
						</div>
					</div>
			<?php
				}
			?>
		</div>
		
	<!-- /.container -->

<?php 
	include('footer.php');
?>   
