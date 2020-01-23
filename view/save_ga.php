<?php 
	session_start();
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
					<h4>Add GA</h4>
				</div>
			</div>
			<form id = "add_ga_form" method = "post" action = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp">
				<div class="well">
					<div class="form-group">
						<span class="error"><?php echo $email; ?></span></br>
						<span class="error">* </span>
						<label for="annoucement_desc" class="cols-sm-2 control-label">Please enter GA email: </label>
						<div class="cols-sm-10">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
								<input type="text" size="210" class="form-control" name="ga_mail" id="ga_mail" value = "<?php echo $_GET['email'] ;?>" placeholder="Enter GA email"/><br>
							</div>
						</div>
					</div>
					<input type = "hidden" name = "star" value = "<?php echo $_GET['starnumber']; ?>" />
					<input type="submit" name = "Submit" id="Submit" value="Save GA" class="btn btn-success"/>
				</div>
			</form>
	<!-- /.container -->

<?php 
	include('footer.php');
?>   
