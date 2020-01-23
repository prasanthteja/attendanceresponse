<?php 
include('header.php');
require_once('/home/mifdo/.php_admin_connect.sphp');
$connection=dbConnect();
mysql_select_db("AttendanceTracking");
?>
    <!-- Page Content -->
    <div class="container">

        <div class="row">
			<div class="thumbnail">
				<div class= "text-over-image">
					<p>Center for Innovation in Teaching & Research</p>
				</div>
				<div class="caption-full">
					<h4>View Notes</h4>
				</div>
			</div>

			<div class="well">
				<div class="table-responsive">  
					<div class="caption-full">
						<span>Note for : <strong><?php echo $_GET['student']; ?></strong></span>
					</div>
					<table id = "note_table" class="table">
						<thead>
							<th id = "note_date" >Date</th>
							<th id = "note_text" >Note</th>
						</thead>
						<tbody>
						<?php
							$star = $_GET['star'];
							$stuecom = $_GET['stuEcom'];
							$noteDate = $_GET['date'];
							$noteSelect = mysql_query("SELECT Date, note FROM notes Where courseStar ='$star' and studentEcom='$stuecom' and date='$noteDate'");
							while($row = mysql_fetch_assoc($noteSelect))
							{
						?>
								<tr>
									<td>
										<?php 
											echo $row['Date'];
										?>
									</td>
									<td>
										<?php 
											echo $row['note'];
										?>
									</td>
								</tr>
						<?php
							}
						?>
						</tbody>
					</table>
				</div>
			</div>
	<!-- /.container -->

<?php 
	include('footer.php');
?>   
