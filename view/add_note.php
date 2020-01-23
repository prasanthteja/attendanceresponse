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
					<h4>Add Note</h4>
				</div>
			</div>
			<form id = "add_ga_form" method = "post" action = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp">
				<div class="well">
					<div class="form-group">
						<label for="annoucement_select_group" class="cols-sm-2 control-label">Select group: </label>
						<div class="cols-sm-10">
							<div class="input-group">
								<select width = 50 name = "stuName" class = "select_group" id = "stuName">
							<?php 
									for($i = 0; $i < count($stud_details); $i++)
									{
								?>
										<option value = "<?php echo $stud_details[$i][1]; ?>"><?php echo $stud_details[$i][0]; ?></option>
								<?php
									}
								?>	
								</select> <br>
							</div>
						</div>
					</div>
					
					<div class="form-group">
						<label for="start_date" class="cols-sm-2 control-label">Select Date: </label>
						<div class="cols-sm-10">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-calendar fa" aria-hidden="true"></i></span>
								<input type="text" class="form-control" name="note_date" id="note_date"  placeholder="Enter Date" value = "" /><br>
							</div>
						</div>
					</div>
		
					<div class="form-group">
						<label for="note" class="cols-sm-2 control-label">Note: </label>
						<div class="cols-sm-10">
							<div class="input-group">
								<span class="input-group-addon"><i class="fa fa-envelope fa" aria-hidden="true"></i></span>
								<textarea class="form-control" name="note" id="note" placeholder="Enter Note" value = "" ></textarea><br>
							</div>
						</div>
					</div>
					<input type = "hidden" name = "star" value = "<?php echo $_GET['starnumber']; ?>" />
					<input type="submit" name = "Submit" id="Submit" value="Save Note" class="btn btn-success"/>
					<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a>
				</div>
			</form>
	<!-- /.container -->

<?php 
	include('footer.php');
?>   
