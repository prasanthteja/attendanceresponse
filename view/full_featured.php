<?php 
	include('header.php');
	$updatedData=false;
	 $testVar=0;
		session_start();
		
			//unset($_SESSION['OldDate']);
	

	function proc($isBtnPress){
		$connection = dbConnect();
		mysql_select_db("AttendanceTracking");

		$star = $_POST['star'];
		if(!empty($_POST['edit_attendance_date']))
			$classdate = $_POST['edit_attendance_date'];
		else
			$classdate  = $_POST['date'];
			//echo $classdate;
			
			//echo $_SESSION["OldDate"];
/*
		echo '<script language="javascript">';

echo 'confirm("BtnPress'.$classdate.'")';

echo '</script>';
*/
		if(strcmp($isBtnPress,"1")==0)
			{
				unset($_SESSION['OldDate']);
/*
			echo '<script language="javascript">';
			echo 'confirm("BtnPress'.$isBtnPress.'")';
			echo '</script>';
*/
		//Testing Email Login---------
			$info_query  = "SELECT title,ecom FROM courseinfo where star ='$star'";
					$info_result = mysql_query($info_query);
					$info_row    = mysql_fetch_array($info_result);
					
					$instrcutor_ecom    = $info_row['ecom'];
					$course_title   = $info_row['title'];
					
						$info_query_1  = "SELECT email FROM users where ecom ='$instrcutor_ecom'";
					$info_result_1 = mysql_query($info_query_1);
					$info_row_1   = mysql_fetch_array($info_result_1);
					
					$instrcutor_email    = $info_row_1['email'];
					
					$sql = "SELECT * FROM attendance where attendedDate='$classdate' and courseStar='$star'";
$result = mysql_query($sql,$connection);
$msgis="The following information was removed from the Attendance Tracking system. Please retain it for you records.\n -----------------------\nCourse: ".$course_title." (".$star.")\nDate: ".$classdate."\n-----------------------\nStudent Name\t\t\tAttendance\n";
if (mysql_num_rows($result) > 0) {
    // output data of each row
    while($row = mysql_fetch_assoc($result)) {
        $msgis.=  $row["StudentName"]. "\t\t\t". $row["attendance"]. "\n";
        //$msgn.="id:";
    }
} else {
    $msgis.= "0 results";
}
					//$msg=$instrcutor_ecom.'-'.$star;

					mail($instrcutor_email,"Regarding Data Deletion for ".$course_title." (".$star.") for Date ".$classdate,$msgis);
		

					
		mysql_query("DELETE FROM attendance WHERE courseStar='$star' AND attendedDate='$classdate'",$connection) or die(mysql_error());
							
			
		

}
else if(strcmp($isBtnPress,"2")==0)
{
	
	
	$connection = dbConnect();
		mysql_select_db("AttendanceTracking");
	$OldDate=$_SESSION["OldDate"];

	
/*
	echo $star;
	echo $classdate;
*/
	//mysql_query("DELETE FROM attendance WHERE courseStar='$star' AND attendedDate='$classdate'",$connection) or die(mysql_error());
		$delete = mysql_query("Delete from attendance where courseStar='$star' AND attendedDate='$OldDate'", $connection) or die("Delete:  " . mysql_error());
	//echo "DELETE  FROM attendance WHERE courseStar='$star' AND attendedDate='$OldDate'";
	unset($_SESSION['OldDate']);
		
	
}
else
{
$connection = dbConnect();
unset($_SESSION['OldDate']);
/*
echo '<script language="javascript">';
		//echo 'confirm("The date for removal of records is not specified. Please provide a date'.$attendance_date'")';
	echo 'confirm("Date Is There'.$isBtnPress.'")';
	//echo 'confirm("The date for removal of records is not specified. Please provide a dat'.$attendance_date.'")';
	//echo "window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp'";
	echo '</script>';
*/
	
		mysql_select_db("AttendanceTracking");
		
		$star = $_POST['star'];
		
		if(!empty($_POST['edit_attendance_date']))
			$classdate = $_POST['edit_attendance_date'];
		else
			$classdate  = $_POST['date'];
		
		$stuList    = explode('|', $_POST['stuList']);
		$msg123     = "";
		$msgheaders = 'From: CITR@wiu.edu' . "\r\n";
		$msgheaders = $msgheaders . 'MIME-Version: 1.0' . "\r\n";
		$msgheaders = $msgheaders . 'Content-type: text/html; charset=utf8' . "\r\n";
		$msg123     = "<html>
	<body>
	<table border=1px>
	<th>
	<tr><td>studentEcom</td>" . "  " . "<td>StudentName</td>" . "  " . "<td>courseStar</td>" . "  " . "<td>Attendance</td>" . "  " . "<td>Date</td>" . "  " . "<td>Rank</td>" . "  " . "<td>LeftEarly(Yes or No)</td></tr></th>" . "\r\n";
		//    $leftflag="N";    
		
		
			$action = '?action=chooseCourseQuick&starnumber=' . $star;
		$count = 0;
		for ($i = 1; $i < count($stuList); $i++) {
			$student    = "student" . $i;
			$left       = "left" . $i;
			if (!empty($_POST[$student]))
				$attendance = $_POST[$student];
			else
				$attendance = $_POST['attendance_status'];

				
			
			$ecom       = base64_decode($stuList[$i - 1]);
			$info       = userldap($ecom);
			$fname      = mysql_real_escape_string($info[0]["givenname"][0]);
			$lname      = mysql_real_escape_string($info[0]["sn"][0]);
			$rank       = mysql_real_escape_string($info[0]["wiuclassification"][0]);
			$cname      = $fname . ' ' . $lname;
			
			if (isset($_POST[$left])) {
				$leftflag = "Y";
			} else {
				$leftflag = "N";
			}
			
		
			

			
			$num_query = mysql_query("SELECT id FROM attendance WHERE courseStar='$star' AND studentEcom='$ecom' AND attendedDate='$classdate' ", $connection);
			$list      = mysql_fetch_row($num_query);
			$aid       = $list[0];
			$num_rows  = mysql_num_rows($num_query);
			
			$update = mysql_query("    UPDATE  attendance
	SET        attendance = '$attendance'
	WHERE courseStar='$star' AND studentEcom='$ecom' AND attendedDate='$classdate'
	", $connection) or die("update:  " . mysql_error());

			$updatedData=true;
			

			if ($num_rows > 0) {
				
				if ($option === "edit") {
					$instecom  = $_SESSION['ecom'];					
					$msgupdate = mysql_query("select subscribed from usersubscriptions where ecom ='$instecom'");
					if (mysql_num_rows($msgupdate) > 0) {
						list($permission) = mysql_fetch_array($msgupdate);
					}
					$coursequery = mysql_query("select cnumber,section from courseinfo where star ='$star' and ecom='$instecom'");
					if (mysql_num_rows($coursequery) > 0) {
						list($coursenumber, $section) = mysql_fetch_array($coursequery);
					}
					$checkquery = mysql_query("select attendance from attendance where courseStar='$star' AND studentEcom='$ecom' AND attendedDate='$classdate'");
					if (mysql_num_rows($checkquery) > 0) {
						list($checkattendance) = mysql_fetch_array($checkquery);
					}
				
					
					$update = mysql_query("    UPDATE  attendance
	SET        attendance = '$attendance'
	WHERE courseStar='$star' AND studentEcom='$ecom' AND attendedDate='$classdate'
	", $connection) or die("update:  " . mysql_error());
	

	
					$emaildate     = $classdate;
					$emailcourse   = $coursestar;
					$emailsubject1 = "Update for Attendance" . "  " . $emaildate . " " . "-" . " " . $coursenumber . "-" . "section#" . $section;
					//echo $permission;
					if ($checkattendance != $attendance) {
						if ($permission == 1) {
							//echo dsc1;
							$msgquery = mysql_query("select * from attendance WHERE courseStar='$star' AND studentEcom='$ecom' AND attendedDate='$classdate'");
							if (mysql_num_rows($msgquery) > 0) {
								while (list($id123, $studentecom1, $studentname1, $courseStar1, $updateattendance1, $attendeddate1, $rank1) = mysql_fetch_array($msgquery)) {
									$ucheck  = 1;
									$ecomset = 1;
									$msg123  = $msg123 . "<tr><td>" . $studentecom1 . "</td>" . "  " . "<td>" . $studentname1 . "</td>" . "  " . "<td>" . $courseStar1 . "</td>" . "  " . "<td style='color:red'><i>" . $updateattendance1 . "</i></td>" . "  " . "<td>" . $attendeddate1 . "</td>" . "  " . "<td>" . $rank . "</td>";
									
								}
							}
						}
					} else {
						if ($permission == 1) {
							//echo dsc1;
							$msgquery = mysql_query("select * from attendance WHERE courseStar='$star' AND studentEcom='$ecom' AND attendedDate='$classdate'");
							if (mysql_num_rows($msgquery) > 0) {
								while (list($id123, $studentecom1, $studentname1, $courseStar1, $updateattendance1, $attendeddate1, $rank1) = mysql_fetch_array($msgquery)) {
									$ucheck  = 1;
									$ecomset = 1;
									$msg123  = $msg123 . "<tr><td>" . $studentecom1 . "</td>" . "  " . "<td>" . $studentname1 . "</td>" . "  " . "<td>" . $courseStar1 . "</td>" . "  " . "<td>" . $updateattendance1 . "</td>" . "  " . "<td>" . $attendeddate1 . "</td>" . "  " . "<td>" . $rank . "</td>";
									
								}
							}
						}
					}
					
					$select = mysql_query("    SELECT *  
	From LeftEarly
	WHERE id='$aid'
	", $connection) or die("update:  " . mysql_error());
					$numrows = mysql_num_rows($select);
			
					if ($numrows > 0) {
						//echo dsc;
						$updateinsert = mysql_query("UPDATE LeftEarly
	SET    LeftEarlyflag = '$leftflag'
	WHERE id='$aid'
	", $connection) or die(mysql_error());
						
						if ($permission == 1) {
							//echo dsc2;
							if (mysql_affected_rows($connection) > 0) {
								//echo dsc1;
								$ucheck2 = 1;
								$msg123  = $msg123 . "<td style='color:red'><i>" . $leftflag . "<i></td></tr>";
							} else {
								if ($ecomset == 1) {
									$ucheck2 = 1;
									$msg123  = $msg123 . "<td>" . "N" . "</td></tr>";
									$ecomset = 0;
								}
							}
						}
					} else {
						
						$updateinsert = mysql_query("INSERT INTO LeftEarly 
	VALUES('$aid','$leftflag')                    
	", $connection) or die("leftearly: " . mysql_error());
					}
					
					//------------------------------- Attendence Preference Email code------------------------//
					$getAbsentCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='absent' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
					$getAbsentCount      = mysql_result($getAbsentCountquery, 0);
					
					$info_query  = "SELECT absenceLimit,studentmail,instructormail,advisormail,beforeabslimit,afterabslimit FROM userpreference WHERE courseinfo ='$star'";
					$info_result = mysql_query($info_query);
					$info_row    = mysql_fetch_array($info_result);
					
					$absenceLimitold    = $info_row['absenceLimit'];
					$studentmail12      = $info_row['studentmail'];
					$instructormail12   = $info_row['instructormail'];
					$advisormail12      = $info_row['advisormail'];
					$beforeabsenselimit = $info_row['beforeabslimit'];
					$afterabsenselimit  = $info_row['afterabslimit'];
					
					if ($absenceLimitold == $getAbsentCount) {
						
						$studentInfo  = ldap_wiuID($ecom);
						$studentEmail = $studentInfo[0]["mail"][0];
						$fname        = $studentInfo[0]["givenname"][0];
						$lname        = $studentInfo[0]["sn"][0];
						
						$title = $fname . ' ' . $lname;
						
						$advisorid = $studentInfo[0]["wiuadvisorid"][0];
						
						$getPresentCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='present' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
						$getPresentCount      = mysql_result($getPresentCountquery, 0);
						$getExcusedCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='excused' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
						$getExcusedCount      = mysql_result($getExcusedCountquery, 0);
						$getTardyCountquery   = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='tardy' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
						$getTardyCount        = mysql_result($getTardyCountquery, 0);
						$getLeftEarlyCount    = mysql_result(mysql_query(" SELECT COUNT(*) FROM attendance inner join LeftEarly on LeftEarly.id=attendance.id WHERE LeftEarlyflag='Y' AND studentEcom='$ecom' AND courseStar='$star'", $connection), 0);
						
						
						
						$gettitlequery = "SELECT cnumber, title from courseinfo where star ='$star'";
						$getresult     = mysql_query($gettitlequery);
						$get_row       = mysql_fetch_array($getresult);
						
						$cnumber      = $get_row['cnumber'];
						$course_title = $get_row['title'];
						
						$query = mysql_query("SELECT ecom FROM courseinfo WHERE star='$star'", $connection) or die(mysql_error());
						$insecom = mysql_result($query, 0);
						
						$instructorquery = mysql_query("SELECT email,fName,lName FROM users WHERE ecom='$insecom'", $connection) or die(mysql_error());
						$get_row1  = mysql_fetch_array($instructorquery);
						$instfname = $get_row1['fName'];
						
						$instlname = $get_row1['lName'];
						
						$instructormail = $get_row1['email'];
						$insttitle      = $instfname . ' ' . $instlname;
						
						$dispDate = date("Y-m-d");
						if ($advisormail12 == 1) {
							$advisorInfo  = ldap_wiuID($advisorid);
							$advisorEmail = $advisorInfo[0]["mail"][0];
							
						}
						
						
						if ($studentmail12 == 1 && $instructormail12 == 1 && $advisormail12 == 1) {
							
							$headers = "From:$instructormail \r\n";
							$headers .= "CC:$advisorEmail \r\n";
							$headers .= "CC:$instructormail \r\n";
							
							$stuemail12 = "$studentEmail \r\n";
							
							$email_subject = "Excessive Absence Notification";
							$msg1          = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
							$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
							$msg1 .= "\r\nDetails : ";
							
							
							
							$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance ,LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
							
							$x = 0;
							while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
								//$msg1.="<br>";                             
								$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
								//$msg .= $row['attendance'].".
								//";
								if ($left == "Y") {
									$msg1 .= ", left early.";
								} else {
									$msg1 .= ".";
								}
								$x = $x + 1;
								echo "<br></br>";
							}
							
							
							mail($stuemail12, $email_subject, $msg1, $headers);
						} else if ($studentmail12 == 1 && $instructormail12 == 1) {
							
							$headers = "From:$instructormail \r\n";
							$headers .= "CC:$instructormail \r\n";
							
							
							$email_subject = "Excessive Absence Notification";
							$stuemail12    = "$studentEmail \r\n";
							
							$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
							$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
							$msg1 .= "\r\nDetails : ";
							
							$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
							
							$x = 0;
							while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
								//$msg1.="<br>";                             
								$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
								//$msg .= $row['attendance'].".
								//";
								if ($left == "Y") {
									$msg1 .= ", left early.";
								} else {
									$msg1 .= ".";
								}
								$x = $x + 1;
								echo "<br></br>";
							}
							
							mail($stuemail12, $email_subject, $msg1, $headers);
						} else if ($studentmail12 == 1 && $advisormail12 == 1) {
							
							$headers = "From:$instructormail \r\n";
							$headers .= "CC:$advisorEmail \r\n";
							
							$email_subject = "Excessive Absence Notification";
							$stuemail12    = "$studentEmail \r\n";
							
							$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
							$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
							$msg1 .= "\r\nDetails : ";
							
							$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
							
							$x = 0;
							while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
								//$msg1.="<br>";                             
								$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
								//$msg .= $row['attendance'].".
								//";
								if ($left == "Y") {
									$msg1 .= ", left early.";
								} else {
									$msg1 .= ".";
								}
								$x = $x + 1;
								echo "<br></br>";
							}
							
							
							mail($stuemail12, $email_subject, $msg1, $headers);
						} else if ($advisormail12 == 1 && $instructormail12 == 1) {
							
							$headers = "From:$instructormail \r\n";
							$headers .= "CC:$advisorEmail \r\n";
							
							
							$email_subject = "Excessive Absence Notification";
							$stuemail12    = "$instructormail \r\n";
							
							$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
							$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
							$msg1 .= "\r\nDetails : ";
							
							$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
							
							$x = 0;
							while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
								//$msg1.="<br>";                             
								$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
								//$msg .= $row['attendance'].".
								//";
								if ($left == "Y") {
									$msg1 .= ", left early.";
								} else {
									$msg1 .= ".";
								}
								$x = $x + 1;
								echo "<br></br>";
							}
							
							
							mail($stuemail12, $email_subject, $msg1, $headers);
						} else if ($studentmail12 == 1) {
							//$headers = "From: " . strip_tags($instrctorEmail) . "\r\n";
							$headers = "From:$instructormail \r\n";
							
							
							$email_subject = "Excessive Absence Notification";
							$stuemail12    = "$studentEmail \r\n";
							
							$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
							$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
							$msg1 .= "\r\nDetails : ";
							
							$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
							
							$x = 0;
							while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
								//$msg1.="<br>";                             
								$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
								//$msg .= $row['attendance'].".
								//";
								if ($left == "Y") {
									$msg1 .= ", left early.";
								} else {
									$msg1 .= ".";
								}
								$x = $x + 1;
								echo "<br></br>";
							}
							
							
							
							mail($stuemail12, $email_subject, $msg1, $headers);
							
						} else if ($instructormail12 == 1) {
							//$headers = "From: " . strip_tags($instrctorEmail) . "\r\n";
							$headers = "From:$instructormail \r\n";
							
							
							$email_subject = "Excessive Absence Notification";
							$stuemail12    = "$instructormail \r\n";
							
							$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
							$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
							$msg1 .= "\r\nDetails : ";
							
							$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
							
							$x = 0;
							while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
								//$msg1.="<br>";                             
								$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
								//$msg .= $row['attendance'].".
								//";
								if ($left == "Y") {
									$msg1 .= ", left early.";
								} else {
									$msg1 .= ".";
								}
								$x = $x + 1;
								echo "<br></br>";
							}
							
							
							mail($stuemail12, $email_subject, $msg1, $headers);
						} else if ($advisormail12 == 1) {
							//$headers = "From: " . strip_tags($instrctorEmail) . "\r\n";
							$headers = "From:$instructormail \r\n";
							
							
							$email_subject = "Excessive Absence Notification";
							$stuemail12    = "$advisorEmail \r\n";
							
							$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
							$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
							$msg1 .= "\r\nDetails : ";
							
							$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
							
							$x = 0;
							while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
								//$msg1.="<br>";                             
								$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
								//$msg .= $row['attendance'].".
								//";
								if ($left == "Y") {
									$msg1 .= ", left early.";
								} else {
									$msg1 .= ".";
								}
								$x = $x + 1;
								echo "<br></br>";
							}
							
							
							mail($stuemail12, $email_subject, $msg1, $headers);
						}
					}
					//**********************code for warning and after************************
					
					else if (($absenceLimitold - 1) == $getAbsentCount) {
						if ($beforeabsenselimit == 1) {
							
							$studentInfo  = ldap_wiuID($ecom);
							$studentEmail = $studentInfo[0]["mail"][0];
							$fname        = $studentInfo[0]["givenname"][0];
							$lname        = $studentInfo[0]["sn"][0];
							
							$title = $fname . ' ' . $lname;
							
							$advisorid = $studentInfo[0]["wiuadvisorid"][0];
							
							$getPresentCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='present' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
							$getPresentCount      = mysql_result($getPresentCountquery, 0);
							$getExcusedCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='excused' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
							$getExcusedCount      = mysql_result($getExcusedCountquery, 0);
							$getTardyCountquery   = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='tardy' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
							$getTardyCount        = mysql_result($getTardyCountquery, 0);
							
							
							$gettitlequery = "SELECT cnumber, title from courseinfo where star ='$star'";
							$getresult     = mysql_query($gettitlequery);
							$get_row       = mysql_fetch_array($getresult);
							
							$cnumber      = $get_row['cnumber'];
							$course_title = $get_row['title'];
							
							$query = mysql_query("SELECT ecom FROM courseinfo WHERE star='$star'", $connection) or die(mysql_error());
							$insecom = mysql_result($query, 0);
							
							$instructorquery = mysql_query("SELECT email,fName,lName FROM users WHERE ecom='$insecom'", $connection) or die(mysql_error());
							$get_row1  = mysql_fetch_array($instructorquery);
							$instfname = $get_row1['fName'];
							
							$instlname = $get_row1['lName'];
							
							$instructormail = $get_row1['email'];
							$insttitle      = $instfname . ' ' . $instlname;
							
							$dispDate = date("Y-m-d");
							
							$headers = "From:$instructormail \r\n";
							$headers .= "CC:$instructormail \r\n";
							
							
							$email_subject = "Excessive Absence Notification";
							$stuemail12    = "$studentEmail \r\n";
							
							$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. With one more absence, you will have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
							$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
							$msg1 .= "\r\nDetails : ";
							
							$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
							
							$x = 0;
							while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
								//$msg1.="<br/>";                             
								$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
								//$msg .= $row['attendance'].".
								//";
								if ($left == "Y") {
									$msg1 .= ", left early.";
								} else {
									$msg1 .= ".";
								}
								$x = $x + 1;
								echo "<br></br>";
							}
							
							
							mail($stuemail12, $email_subject, $msg1, $headers);
							
						}
					} else if ($getAbsentCount > $absenceLimitold) {
						if ($afterabsenselimit == 1) {
							$studentInfo  = ldap_wiuID($ecom);
							$studentEmail = $studentInfo[0]["mail"][0];
							$fname        = $studentInfo[0]["givenname"][0];
							$lname        = $studentInfo[0]["sn"][0];
							
							$title = $fname . ' ' . $lname;
							
							$advisorid = $studentInfo[0]["wiuadvisorid"][0];
							
							$getPresentCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='present' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
							$getPresentCount      = mysql_result($getPresentCountquery, 0);
							$getExcusedCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='excused' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
							$getExcusedCount      = mysql_result($getExcusedCountquery, 0);
							$getTardyCountquery   = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='tardy' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
							$getTardyCount        = mysql_result($getTardyCountquery, 0);
							
							
							$gettitlequery = "SELECT cnumber, title from courseinfo where star ='$star'";
							$getresult     = mysql_query($gettitlequery);
							$get_row       = mysql_fetch_array($getresult);
							
							$cnumber      = $get_row['cnumber'];
							$course_title = $get_row['title'];
							
							$query = mysql_query("SELECT ecom FROM courseinfo WHERE star='$star'", $connection) or die(mysql_error());
							$insecom = mysql_result($query, 0);
							
							$instructorquery = mysql_query("SELECT email,fName,lName FROM users WHERE ecom='$insecom'", $connection) or die(mysql_error());
							$get_row1  = mysql_fetch_array($instructorquery);
							$instfname = $get_row1['fName'];
							
							$instlname = $get_row1['lName'];
							
							$instructormail = $get_row1['email'];
							$insttitle      = $instfname . ' ' . $instlname;
							
							$dispDate = date("Y-m-d");
							//$headers = "From: " . strip_tags($instrctorEmail) . "\r\n";
							$headers  = "From:$instructormail \r\n";
							//    $headers = "From:siddhardha999@gmail.com \r\n";
							
							$email_subject = "Excessive Absence Notification";
							$stuemail12    = "$studentEmail \r\n";
							
							$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. Let this serve as a notice that you have EXCEEDED this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
							
							$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
							$msg1 .= "\r\nDetails : ";
							
							$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
							
							$x = 0;
							while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
								//$msg1.="<br>";                             
								$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
								//$msg .= $row['attendance'].".
								//";
								if ($left == "Y") {
									$msg1 .= ", left early.";
								} else {
									$msg1 .= ".";
								}
								$x = $x + 1;
								echo "<br></br>";
							}
							
							
							mail($stuemail12, $email_subject, $msg1, $headers);
							
						}
					}
					$msg = "The attendance was successfully updated.";
					//----------------------------------
					
					if (mysql_affected_rows($connection) === 1)
						$count = $count + 1;
				} else {
					$msg    = "Attendance for " . $classdate . " has already been entered. Please try again with different date.";
					$status = "";
				}
				
			}
			
			
			else {
/*
				if($option=="edit") {
				
				mysql_query("DELETE FROM attendance WHERE courseStar='$star' AND attendedDate='$classdate'",$connection) or die(mysql_error());
				}
*/
				
				//mail("sc-devineni@wiu.edu","test","hello");
				$coursequery = mysql_query("select cnumber,section from courseinfo where star ='$star' and ecom='$instecom'");
				if (mysql_num_rows($coursequery) > 0) {
					list($coursenumber, $section) = mysql_fetch_array($coursequery);
				}
				$emaildate     = $classdate;
				$emailcourse   = $coursestar;
				$emailsubject1 = "Addition of Attendance" . "  " . $emaildate . " " . "-" . " " . $coursenumber . "-" . "section#" . $section;
				$instecom      = $_SESSION['ecom'];
				$msgupdate     = mysql_query("select subscribed from usersubscriptions where ecom ='$instecom'");
				if (mysql_num_rows($msgupdate) > 0) {
					list($permission) = mysql_fetch_array($msgupdate);
				}
				$insert = mysql_query("INSERT INTO attendance VALUES('','$ecom', '$cname', '$star','$attendance','$classdate','$rank')", $connection) or die(mysql_error());
				
				if ($permission == 1) {
					$check  = 1;
					$msg123 = $msg123 . "<tr><td>" . $ecom . "</td>" . "  " . "<td>" . $cname . "</td>" . "  " . "<td>" . $star . "</td>" . "  " . "<td>" . $attendance . "</td>" . "  " . "<td>" . $classdate . "</td>" . "  " . "<td>" . $rank . "</td>";
				}
				$q1 = mysql_query("SELECT max(id) FROM attendance WHERE courseStar='$star' AND studentEcom='$ecom'", $connection) or die(mysql_error());
				$list = mysql_fetch_row($q1);
				$id   = $list[0];
				mysql_query("INSERT INTO LeftEarly VALUES('$id','$leftflag')", $connection) or die(mysql_error());
				if ($permission == 1) {
					$check2 = 1;
					$msg123 = $msg123 . "  " . "<td>" . $leftflag . "</td></tr>" . "\r\n";
					
				}
				
				$msg = "The attendance was successfully entered.";
				
				if (mysql_affected_rows($connection) == 1)
					$count = $count + 1;
				
				else
					$msg = "Attendance for " . $classdate . " has already been entered. Please try again with different date.";
				//------------------------------- Attendence Preference Email code------------------------//
				$getAbsentCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='absent' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
				$getAbsentCount      = mysql_result($getAbsentCountquery, 0);
				
				$info_query         = "SELECT absenceLimit,studentmail,instructormail,advisormail,beforeabslimit,afterabslimit FROM userpreference WHERE courseinfo ='$star'";
				$info_result        = mysql_query($info_query);
				$info_row           = mysql_fetch_array($info_result);
				//echo $info_query;
				$absenceLimitold    = $info_row['absenceLimit'];
				$studentmail12      = $info_row['studentmail'];
				$instructormail12   = $info_row['instructormail'];
				$advisormail12      = $info_row['advisormail'];
				$beforeabsenselimit = $info_row['beforeabslimit'];
				$afterabsenselimit  = $info_row['afterabslimit'];
				
				
				if ($absenceLimitold == $getAbsentCount) {
					
					$studentInfo  = ldap_wiuID($ecom);
					$studentEmail = $studentInfo[0]["mail"][0];
					$fname        = $studentInfo[0]["givenname"][0];
					$lname        = $studentInfo[0]["sn"][0];
					
					$title = $fname . ' ' . $lname;
					
					$advisorid = $studentInfo[0]["wiuadvisorid"][0];
					
					$getPresentCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='present' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
					$getPresentCount      = mysql_result($getPresentCountquery, 0);
					$getExcusedCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='excused' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
					$getExcusedCount      = mysql_result($getExcusedCountquery, 0);
					$getTardyCountquery   = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='tardy' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
					$getTardyCount        = mysql_result($getTardyCountquery, 0);
					
					
					$gettitlequery = "SELECT cnumber, title from courseinfo where star ='$star'";
					$getresult     = mysql_query($gettitlequery);
					$get_row       = mysql_fetch_array($getresult);
					
					$cnumber      = $get_row['cnumber'];
					$course_title = $get_row['title'];
					
					$query = mysql_query("SELECT ecom FROM courseinfo WHERE star='$star'", $connection) or die(mysql_error());
					$insecom = mysql_result($query, 0);
					
					$instructorquery = mysql_query("SELECT email,fName,lName FROM users WHERE ecom='$insecom'", $connection) or die(mysql_error());
					$get_row1  = mysql_fetch_array($instructorquery);
					$instfname = $get_row1['fName'];
					
					$instlname = $get_row1['lName'];
					
					$instructormail = $get_row1['email'];
					$insttitle      = $instfname . ' ' . $instlname;
					
					$dispDate = date("Y-m-d");
					if ($advisormail12 == 1) {
						$advisorInfo  = ldap_wiuID($advisorid);
						$advisorEmail = $advisorInfo[0]["mail"][0];
						
					}
					
					
					if ($studentmail12 == 1 && $instructormail12 == 1 && $advisormail12 == 1) {
						
						$headers = "From:$instructormail \r\n";
						$headers .= "CC:$advisorEmail \r\n";
						$headers .= "CC:$instructormail \r\n";
						$email_subject = "Excessive Absence Notification";
						$stuemail12    = "$studentEmail \r\n";
						
						$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
						$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
						$msg1 .= "\r\nDetails : ";
						
						$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
						
						$x = 0;
						while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
							//$msg1.="<br>";                             
							$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
							//$msg .= $row['attendance'].".
							//";
							if ($left == "Y") {
								$msg1 .= ", left early.";
							} else {
								$msg1 .= ".";
							}
							$x = $x + 1;
							echo "<br></br>";
						}
						
						
						mail($stuemail12, $email_subject, $msg1, $headers);
					} else if ($studentmail12 == 1 && $instructormail12 == 1) {
						
						$headers = "From:$instructormail \r\n";
						$headers .= "CC:$instructormail \r\n";
						
						
						$email_subject = "Excessive Absence Notification";
						$stuemail12    = "$studentEmail \r\n";
						
						$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
						$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
						$msg1 .= "\r\nDetails : ";
						
						$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
						
						$x = 0;
						while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
							//$msg1.="<br>";                             
							$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
							//$msg .= $row['attendance'].".
							//";
							if ($left == "Y") {
								$msg1 .= ", left early.";
							} else {
								$msg1 .= ".";
							}
							$x = $x + 1;
							echo "<br></br>";
						}
						
						
						mail($stuemail12, $email_subject, $msg1, $headers);
					} else if ($studentmail12 == 1 && $advisormail12 == 1) {
						
						$headers = "From:$instructormail \r\n";
						$headers .= "CC:$advisorEmail \r\n";
						
						$email_subject = "Excessive Absence Notification";
						$stuemail12    = "$studentEmail \r\n";
						
						$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
						$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
						$msg1 .= "\r\nDetails : ";
						
						$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
						
						$x = 0;
						while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
							//$msg1.="<br>";                             
							$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
							//$msg .= $row['attendance'].".
							//";
							if ($left == "Y") {
								$msg1 .= ", left early.";
							} else {
								$msg1 .= ".";
							}
							$x = $x + 1;
							echo "<br></br>";
						}
						
						
						mail($stuemail12, $email_subject, $msg1, $headers);
					} else if ($advisormail12 == 1 && $instructormail12 == 1) {
						
						$headers = "From:$instructormail \r\n";
						$headers .= "CC:$advisorEmail \r\n";
						
						
						$email_subject = "Excessive Absence Notification";
						$stuemail12    = "$instructormail \r\n";
						
						$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
						$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
						$msg1 .= "\r\nDetails : ";
						
						$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
						
						$x = 0;
						while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
							//$msg1.="<br>";                             
							$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
							//$msg .= $row['attendance'].".
							//";
							if ($left == "Y") {
								$msg1 .= ", left early.";
							} else {
								$msg1 .= ".";
							}
							$x = $x + 1;
							echo "<br></br>";
						}
						
						
						mail($stuemail12, $email_subject, $msg1, $headers);
					} else if ($studentmail12 == 1) {
						//$headers = "From: " . strip_tags($instrctorEmail) . "\r\n";
						$headers = "From:$instructormail \r\n";
						
						
						$email_subject = "Excessive Absence Notification";
						$stuemail12    = "$studentEmail \r\n";
						
						$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
						$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
						$msg1 .= "\r\nDetails : ";
						
						$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
						
						$x = 0;
						while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
							//$msg1.="<br>";                             
							$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
							//$msg .= $row['attendance'].".
							//";
							if ($left == "Y") {
								$msg1 .= ", left early.";
							} else {
								$msg1 .= ".";
							}
							$x = $x + 1;
							echo "<br></br>";
						}
						mail($stuemail12, $email_subject, $msg1, $headers);
						
					} else if ($instructormail12 == 1) {
						//$headers = "From: " . strip_tags($instrctorEmail) . "\r\n";
						$headers       = "From:$instructormail \r\n";
						$email_subject = "Excessive Absence Notification";
						$stuemail12    = "$instructormail \r\n";
						
						$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
						$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
						$msg1 .= "\r\nDetails : ";
						
						$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
						
						$x = 0;
						while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
							//$msg1.="<br>";                             
							$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
							//$msg .= $row['attendance'].".
							//";
							if ($left == "Y") {
								$msg1 .= ", left early.";
							} else {
								$msg1 .= ".";
							}
							$x = $x + 1;
							echo "<br></br>";
						}
						
						
						mail($stuemail12, $email_subject, $msg1, $headers);
					} else if ($advisormail12 == 1) {
						//$headers = "From: " . strip_tags($instrctorEmail) . "\r\n";
						$headers = "From:$instructormail \r\n";
						
						
						$email_subject = "Excessive Absence Notification";
						$stuemail12    = "$advisorEmail \r\n";
						
						$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. You have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
						$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
						$msg1 .= "\r\nDetails : ";
						
						$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
						
						$x = 0;
						while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
							//$msg1.="<br>";                             
							$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
							//$msg .= $row['attendance'].".
							//";
							if ($left == "Y") {
								$msg1 .= ", left early.";
							} else {
								$msg1 .= ".";
							}
							$x = $x + 1;
							echo "<br></br>";
						}
						
						
						mail($stuemail12, $email_subject, $msg1, $headers);
					}
				}
				//**********************code for warning and after************************
				
				if (($absenceLimitold - 1) == $getAbsentCount) {
					if ($beforeabsenselimit == 1) {
						$studentInfo  = ldap_wiuID($ecom);
						$studentEmail = $studentInfo[0]["mail"][0];
						$fname        = $studentInfo[0]["givenname"][0];
						$lname        = $studentInfo[0]["sn"][0];
						
						$title = $fname . ' ' . $lname;
						
						$advisorid = $studentInfo[0]["wiuadvisorid"][0];
						
						$getPresentCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='present' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
						$getPresentCount      = mysql_result($getPresentCountquery, 0);
						$getExcusedCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='excused' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
						$getExcusedCount      = mysql_result($getExcusedCountquery, 0);
						$getTardyCountquery   = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='tardy' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
						$getTardyCount        = mysql_result($getTardyCountquery, 0);
						
						
						$gettitlequery = "SELECT cnumber, title from courseinfo where star ='$star'";
						$getresult     = mysql_query($gettitlequery);
						$get_row       = mysql_fetch_array($getresult);
						
						$cnumber      = $get_row['cnumber'];
						$course_title = $get_row['title'];
						
						$query = mysql_query("SELECT ecom FROM courseinfo WHERE star='$star'", $connection) or die(mysql_error());
						$insecom = mysql_result($query, 0);
						
						$instructorquery = mysql_query("SELECT email,fName,lName FROM users WHERE ecom='$insecom'", $connection) or die(mysql_error());
						$get_row1  = mysql_fetch_array($instructorquery);
						$instfname = $get_row1['fName'];
						
						$instlname = $get_row1['lName'];
						
						$instructormail = $get_row1['email'];
						$insttitle      = $instfname . ' ' . $instlname;
						
						$dispDate = date("Y-m-d");
						
						$headers = "From:$instructormail \r\n";
						$headers .= "CC:$instructormail \r\n";
						$email_subject = "Excessive Absence Notification";
						$stuemail12    = "$studentEmail \r\n";
						
						$msg1 = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. With one more absence, you will have reached this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
						$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
						$msg1 .= "\r\nDetails : ";
						
						$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
						
						$x = 0;
						while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
							//$msg1.="<br>";                             
							$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
							//$msg .= $row['attendance'].".
							//";
							if ($left == "Y") {
								$msg1 .= ", left early.";
							} else {
								$msg1 .= ".";
							}
							$x = $x + 1;
							echo "<br></br>";
						}
						
						
						mail($stuemail12, $email_subject, $msg1, $headers);
					}
				}
				if ($getAbsentCount > $absenceLimitold) {
					if ($afterabsenselimit == 1) {
						$studentInfo  = ldap_wiuID($ecom);
						$studentEmail = $studentInfo[0]["mail"][0];
						$fname        = $studentInfo[0]["givenname"][0];
						$lname        = $studentInfo[0]["sn"][0];
						
						$title = $fname . ' ' . $lname;
						
						$advisorid = $studentInfo[0]["wiuadvisorid"][0];
						
						$getPresentCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='present' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
						$getPresentCount      = mysql_result($getPresentCountquery, 0);
						$getExcusedCountquery = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='excused' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
						$getExcusedCount      = mysql_result($getExcusedCountquery, 0);
						$getTardyCountquery   = mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='tardy' AND studentEcom='$ecom' AND courseStar='$star'", $connection);
						$getTardyCount        = mysql_result($getTardyCountquery, 0);
						
						
						$gettitlequery = "SELECT cnumber, title from courseinfo where star ='$star'";
						$getresult     = mysql_query($gettitlequery);
						$get_row       = mysql_fetch_array($getresult);
						
						$cnumber      = $get_row['cnumber'];
						$course_title = $get_row['title'];
						
						$query = mysql_query("SELECT ecom FROM courseinfo WHERE star='$star'", $connection) or die(mysql_error());
						$insecom = mysql_result($query, 0);
						
						$instructorquery = mysql_query("SELECT email,fName,lName FROM users WHERE ecom='$insecom'", $connection) or die(mysql_error());
						$get_row1  = mysql_fetch_array($instructorquery);
						$instfname = $get_row1['fName'];
						
						$instlname = $get_row1['lName'];
						
						$instructormail = $get_row1['email'];
						$insttitle      = $instfname . ' ' . $instlname;
						
						$dispDate = date("Y-m-d");
						//$headers = "From: " . strip_tags($instrctorEmail) . "\r\n";
						$headers  = "From:$instructormail \r\n";
						//    $headers = "From:siddhardha999@gmail.com \r\n";
						
						$email_subject = "Excessive Absence Notification";
						$stuemail12    = "$studentEmail \r\n";
						$msg1          = " Hello $fname $lname ,
		
	Your instructor, $insttitle, has designated $getAbsentCount absence(s) as being excessive. Let this serve as a notice that you have EXCEEDED this number for the $course_title. A record of your class attendance is shown below. Chronic absences can affect your academic success. You are encouraged to work with your instructor and adviser so you don't fall behind.  \r\n
	For prolonged absences, you should consider contacting the Student Development and Orientation Office at 309/298-1884 (http://wiu.edu/student_services/student_development_office/).";
						
						$msg1 .= " Your attendance details for $course_title - $cnumber as of $dispDate.    
	Attendance Summary: 
		
	Present:  $getPresentCount days 
	Absent :  $getAbsentCount days 
	Excused:  $getExcusedCount days
	Tardy  :  $getTardyCount days 
	Left Early: $getLeftEarlyCount days";
						$msg1 .= "\r\nDetails : ";
						
						$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
						
						$x = 0;
						while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
							//$msg1.="<br>";                             
							$msg1 .= "\r\n\t" . $attendedDate . "~" . $attendance;
							//$msg .= $row['attendance'].".
							//";
							if ($left == "Y") {
								$msg1 .= ", left early.";
							} else {
								$msg1 .= ".";
							}
							$x = $x + 1;
							echo "<br></br>";
						}
						
						mail($stuemail12, $email_subject, $msg1, $headers);
						
					}
					
				}
				$uid                         = $_SESSION['ecom'];
				$studentInfo                 = ldap_wiuID($ecom);
				$studentEmail                = $studentInfo[0]["mail"][0];
				$getEmailAbsentPreference    = mysql_query("SELECT eachabsent FROM userpreference WHERE ecom='$uid' AND courseinfo='$star'", $connection);
				$getEmailAbsentPreferenceVal = mysql_fetch_array($getEmailAbsentPreference);
				$emailAbsentPreference       = $getEmailAbsentPreferenceVal['eachabsent'];
				if ($emailAbsentPreference == "1") {
					$gettitleinfoquery = "SELECT cnumber, title from courseinfo where star ='$star'";
					$gettitletresult   = mysql_query($gettitleinfoquery);
					$getTitleVal       = mysql_fetch_array($gettitletresult);
					$cnumber           = $getTitleVal['cnumber'];
					$courseTitle       = $getTitleVal['title'];
					$instructorquery = mysql_query("SELECT email,fName,lName FROM users WHERE ecom='$uid'", $connection) or die(mysql_error());
					$get_row1       = mysql_fetch_array($instructorquery);
					$instfname      = $get_row1['fName'];
					$instlname      = $get_row1['lName'];
					$instructormail = $get_row1['email'];
					$insttitle      = $instfname . ' ' . $instlname;
					if ($attendance == "absent") {
						$to = $studentEmail;
						
						$subject = 'You were marked as absent in ' . $courseTitle . ' today';
						$message = 'This is to inform you that you were marked as absent by your instructor (' . $insttitle . ') today. If you are experiencing circumstances that will require an extended leave from class, you are encourage to contact the Student Development Office at 309/298-1884. Additionally, you attendance history for this class is provided below:';
						
						
						$message .= "\r\nDetails : ";
						
						$getDetails = mysql_query("SELECT 
	DATE_FORMAT(attendedDate,'%M %d, %Y %a') as attendedDate , attendance, LeftEarlyflag
	FROM 
	attendance 
	Left join
	LeftEarly
	on
	attendance.id=LeftEarly.id
	WHERE 
	studentEcom='$ecom' 
	AND 
	courseStar='$star' 
	ORDER BY 
	attendedDate 
	", $connection) or die(mysql_error());
						
						$x = 0;
						while (list($attendedDate, $attendance, $left) = mysql_fetch_array($getDetails)) {
							$message .= "<br>";
							$message .= "\r\n\t" . $attendedDate . "~" . $attendance;
							//$msg .= $row['attendance'].".
							//";
							if ($left == "Y") {
								$message .= ", left early.";
							} else {
								$message .= ".";
							}
							$x = $x + 1;
							$message .= "<br></br>";
						}
						// To send HTML mail, the Content-type header must be set
						//$headers  = 'MIME-Version: 1.0' . "\r\n";
						//$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
						// Additional headers
						$headers = 'From: CITR@wiu.edu' . "\r\n";
						mail($to, $subject, $message, $headers);
					}
				}
				
				//----------------------------------
				
			}
		}
		$msg123 = $msg123 . "</table></body></html>";
		if (($check == 1 && $check2 == 1) || ($ucheck == 1 || $ucheck2 == 1)) {
			mail($instructormail12, $emailsubject1, $msg123, $headers);
			
			$check   = 0;
			$check2  = 0;
			$ucheck  = 0;
			$ucheck2 = 0;
			
		}
		
		mysql_close($connection);
	

		
/*
echo '<script language="javascript">';
echo 'confirm("Data Is Updated Successfully!!!")';
echo '</script>';
*/
}
	}
	
/*
	function newMethod($isBtnPress)
	{
			echo $date;
			if($isBtnPress)
			{
			echo '<script language="javascript">';

echo 'confirm("BtnPress'.$date.'")';

echo '</script>';
}
*/
				
/*
	if(isset($_GET['Remove All Records for This Day']))
	{
		echo "HIHIH";
		
		newMethod();
		}
*/
?>
    <!-- Page Content -->
    
  <?php echo $updatedData;?>
    <div class="container">

        <div class="row">
			<div class="thumbnail">
				<div class= "text-over-image">
					<p>Center for Innovation in Teaching & Research</p>
				</div>
				<div class="caption-full">
					<h4>Full Featured Attendance</h4>
				</div>
			</div>
<?php $connection = dbConnect();
		$date  = $_POST['date'];
								if($testVar==0)
								{
									$testVar= $attendance_date;
									//echo $testVar;
									
								}

		mysql_select_db("AttendanceTracking");
		$alert_query = mysql_query("SELECT id FROM attendance WHERE courseStar='".$_GET['starnumber']."'  AND attendedDate='".$date."' ", $connection);
		//echo $date;
		
	$num_rows1  = mysql_num_rows($alert_query); 
 
	?>
			<div class="well padding_2">
			<form id = "attendance_quick_form" method = "post" action = "">
				<?php
					if($operation === "update")
					{
							proc();
				?>
						<input type="submit" name="Submit" id="Submit" value="Save" class="btn btn-success"/>
						<input type="submit" name="Submit" id="Submit" value="Save and Enter Another" class="btn btn-success"/>
				<?php
					}
					if ($operation === "edit")
					{
						//echo $attendance_date; 
						//proc();
						//newMethod($attendance_date);
						
				?>
					
					<input type="submit" name="Submit" id="Submit" value="Save Changes" class="btn btn-success"/>
					<input type="submit" name="Submit_B" id="Submit_B" value="Remove All Records for This Day" class="btn btn-success"/>
				
					
				<?php
					
					}
				?>
				<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a> <br> <br>
			
					<label for = "from-input">Date : </label>
					<?php 
						if($operation == "edit")
						{
					?>
							<input type = "text" id = "edit_attendance_date" name = "date" value = "<?php echo $attendance_date;?>">
							
							
							
					<?php
						
						}
						else
						{
					?>
							<input type = "text" id = "date"  name = "date" placeholder = "Click here to select dates" >
					<?php
												}
					?>
				
					<div class="table-responsive">          
						<table id = "attendance_quick_table">
							<thead>
								<th id = "stud_name" >Student Name</th>
								<th id = "stud_pr" >Present</th>
								<th id = "stud_ab" >Absent</th>
								<th id = "stud_ea" >Excused Absence</th>
								<th id = "stud_tr" >Tardy</th>
								<th id = "stud_le" >Left Early</th>
							</thead>
							<tbody>
							<?php
								$i = 1;
								foreach($stud_rec_array as $values)
								{
									//echo $stud_rec_array;
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
								//	echo $values[12];
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
										$present_checked = "checked";
									
									if($values[12] == "absent")
										$absent_checked = "checked";
									
									if($values[12] == "excused")
										$excused_checked = "checked";
									
									if($values[12] == "tardy")
										$tardy_checked = "checked";
										
										

										
										
									
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
														<i class="fa fa-2x fa-volume-up" aria-hidden="true" title = "Speak" onclick="responsiveVoice.speak('<?php echo $values[0]; ?>','US English Female');"></i>
													<?php
														if(!empty($values[15]) )
														{
													?>
													
															<a href = "https://www.wiu.edu/citr/resources/students/index.sphp?uid=<?php echo $values[14]; ?>&starnumber=<?php echo $_GET['starnumber'];?>&studEcom=<?php echo $values[16];?>" target = "_blank" title = "Text/Message" ><i class="fa fa-2x fa-comment" aria-hidden="true"></i></a>
													<?php
														}
													?>
														<a class = "email_icon" href="<?php echo $values[6]; ?>" target = "_blank" title = "E-Mail" ><i class="fa fa-2x fa-envelope" aria-hidden="true"></i></a>
														<a href="<?php echo $values[2]; ?>" target = "_blank" title = "Refer" ><img height = "28" width = "28" src="/citr/AttendanceResponsive/images/refer.png" /></a>
														<a href="https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=getAdvisorDetails&starnumber=<?php echo $star_number; ?>&ecom=<?php echo $values[16]; ?>" target = "_blank" title = "Student Advisor" ><img height = "28" width = "28" src="/citr/AttendanceResponsive/images/Advisor.png" /></a>
													
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
											<input id = "stud_pr_<?php echo $studName . "_" . $i; ?>" checked="checked" value="present" name="student<?php echo $i; ?>" type="radio" <?php echo $present_checked; ?> >
											<label for = "stud_pr_<?php echo $studName . "_" . $i; ?>" >
												<span></span>
											</label>
										</td>
										<td class = "hidden-sm-up" >
											<input id = "stud_ab_<?php echo $studName . "_" . $i; ?>" value="absent" name="student<?php echo $i; ?>" type="radio" <?php echo $absent_checked; ?> >
											<label for = "stud_ab_<?php echo $studName . "_" . $i; ?>" >
												<span></span>
											</label>
										</td>
										<td>
											<input id = "stud_ea_<?php echo $studName . "_" . $i; ?>" value="excused" name="student<?php echo $i; ?>" type="radio" <?php echo $excused_checked; ?> >
											<label for = "stud_ea_<?php echo $studName . "_" . $i; ?>" >
												<span></span>
											</label>
										</td>
										<td>
											<input id = "stud_tr_<?php echo $studName . "_" . $i; ?>" value="tardy" name="student<?php echo $i; ?>" type="radio" <?php echo $tardy_checked; ?> >
											<label for = "stud_tr_<?php echo $studName . "_" . $i; ?>" >
												<span></span>
											</label>
											
										</td>
										
										<td>
										<input id = "stud_le_<?php echo $studName . "_" . $i; ?>" value="LeftEarly" name = "left<?php echo $i; ?>" type="checkbox" <?php echo $left_early; ?> >
											<label for = "stud_le_<?php echo $studName . "_" . $i; ?>" ></label>
										</td>
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

							//proc();
					?>
							<input type="submit" name="Submit" id="Submit" value="Save" class="btn btn-success"/>
							<input type="submit" name="Submit" id="Submit" value="Save and Enter Another" class="btn btn-success"/>
					<?php
						}
						if ($operation === "edit")
						{
					?>
						<input type="submit" name="Submit" id="Submit" value="Save Changes" class="btn btn-success"/>
						<input type="submit" name="Submit_B" id="Submit_B" onclick="myFunction()" value="Remove All Records for This Day" class="btn btn-success"/>

						<?php
						}
					?>
					<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a>
				</form>
		</div>
	<!-- /.container -->
	
	<script>
		function myFunction()
		{
			
			
/*
			if(<?php strlen($attendance_date)<1?>)
			{
				confirm("No Date");
			}
*/

		}
		
		
		
		</script>
<?php 
	session_start();
	
	//echo $classdate;
	//echo $date;
/*
	if(!empty($_POST['edit_attendance_date']))
			$classdate = $_POST['edit_attendance_date'];
		else
			$classdate  = $_POST['date'];
*/
		//echo $testVar;
		define("OldDate", $testVar);
		
		if($_SESSION["OldDate"]=="")
		{
			$_SESSION["OldDate"]=$testVar;
		}
		//echo OldDate;
//		proc("2");



	if($num_rows1>0 && isset($_POST['Submit'])){
		
			echo '<script language="javascript">';
		echo 'alert("The Data Is Updated ...you are Redirecting to Main Page'.$testVar.'")';
	//echo "window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp'";
	echo '</script>';

	proc("0");
	
	//header("Location=https://www.google.com",true);
	echo '<script language="javascript">';
//echo 'confirm("Please Refresh page to see changes")';
	echo "window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp'";
	echo '</script>';

} 
    elseif($num_rows1>0 && isset($_POST['Submit_B'])&&$attendance_date==""){
	echo '<script language="javascript">';
		echo 'alert("The Data is deleted... you are Redirecting to Main Page")';
	//echo "window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp'";
	echo '</script>';
	proc("1");
	echo '<script language="javascript">';
		//echo 'alert("The Data Is Updated ...you are Redirecting to Main Page")';
		echo "window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp'";
	echo '</script>';
	}
	elseif($date!='')
	{
/*
	echo '<script language="javascript">';
		//echo 'confirm("The date for removal of records is not specified. Please provide a date'.$attendance_date'")';
	echo 'confirm("Date There'.$date.'")';
	//echo 'confirm("The date for removal of records is not specified. Please provide a dat'.$attendance_date.'")';
	//echo "window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp'";
	echo '</script>';
	echo '<script language="javascript">';
		//echo 'confirm("The date for removal of records is not specified. Please provide a date'.$attendance_date'")';
	echo 'confirm("Date Is There'.$testVar.'")';
	//echo 'confirm("The date for removal of records is not specified. Please provide a dat'.$attendance_date.'")';
	//echo "window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp'";
	echo '</script>';
*/
echo '<script language="javascript">';
		echo 'alert("The data is updating… please click OK to continue.")';
	//echo "window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp'";
	echo '</script>';

	proc("2");
	proc("0");
	
	echo '<script language="javascript">';
// 		echo 'alert("The Data Is Updated ...you are Redirecting to Main Page")';
		echo "window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp'";
	echo '</script>';
		
	

}
elseif((isset($_POST['Submit'])&&$date=='')||isset($_POST['Submit_B'])&&$date=='')
	{
			echo '<script language="javascript">';
		//echo 'confirm("The date for removal of records is not specified. Please provide a date'.$attendance_date'")';
	echo 'confirm("The date for removal of records is not specified. Please provide a date'.$date.'")';
		//echo 'confirm("The date for removal of records is not specified. Please provide a dat'.$attendance_date.'")';
	//echo "window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp'";
	echo '</script>';
	

} 
 
include('footer.php');
?>


<!--
// build js

/*
echo "<script language=\"javascript\">";

echo "var question=confirm(\"Process ?\");";

echo "if(question)";
 echo "{".proc("1")."}";
   echo " else { document.write(\" you selected no\"); }";

echo "</script>";
*/
-->



	


