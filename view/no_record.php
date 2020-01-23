<?php 
include('header.php');
header("refresh:5; url=https://www.wiu.edu/citr/AttendanceResponsive/index.sphp");
?>
    <!-- Page Content -->
    <div class="container">

        <div class="row">
			<div class="thumbnail">
				<div class= "text-over-image">
					<p>Center for Innovation in Teaching & Research</p>
				</div>
				<div class="caption-full">
					<h4>Quick Attendance</h4>
				</div>
			</div>

			<div class="well">
				
				<?php 
					if(isset($content))
					{
				?>
						<span> <?php echo $content;?> </span>
				<?php
					}
					else
					{
				?>
						<span> No record found. </span>
				<?php		
					}
				?>
				<span> You will be redirected in 5 seconds. </span><br>
				<span> If not </span><a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "">Click here</a>
			</div>
	<!-- /.container -->

<?php 
	include('footer.php');
?>   
