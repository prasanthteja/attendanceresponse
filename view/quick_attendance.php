<?php 
	include('header.php');

		function proc(){
			
		$connection = dbConnect();
		$date  = $_POST['date'];
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
	<tr><td>studentEcom</td>" . "  " . "<td>StudentName</td>" . "  " . "<td>courseStar</td>" . "  " . "<td>Attendance</td>" . "  " . "<td>Date</td>" . "  " . "<td>Rank</td>" . "  " . "<td>LeftEarly(Yes or NO)</td></tr></th>" . "\r\n";
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
			
			if ($num_rows > 0) {
				
				if ($option === "edit") {
					$instecom  = $_SESSION['ecom'];
					//echo dsc;
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
	Leftt Early: $getLeftEarlyCount days";
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
							
							//mail("s-kantamneni@wiu.edu", $email_subject, $msg1);
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
				/*if($option=="edit") {
				
				mysql_query("DELETE FROM attendance WHERE courseStar='$star' AND attendedDate='$classdate'",$connection) or die(mysql_error());....
				}*/
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
			// mail("sc-devineni@wiu.edu",$emailsubject1, $msg123,$msgheaders);
			$check   = 0;
			$check2  = 0;
			$ucheck  = 0;
			$ucheck2 = 0;
			
		}
		//mail("R-Runquist@wiu.edu",$emailsubject1,$msg123,$msgheaders);
		mysql_close($connection);

		}
?>

<head>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
@import url(https://fonts.googleapis.com/css?family=Noto+Sans);

body{
  background: darken(#04B486, 30%);
/*   font-family: 'Noto Sans', 'Helvetica'; */
  
}

.customAlert{
	
  display: none;
  position: fixed;
  max-width: 32%;
  min-width: 250px !important;
  min-height: 26%;
  height: 200px;
  left: 50%;
  top: 50%;
  padding: 0px;
  box-sizing: border-box;
  margin-left: -12.5%;
  margin-top: -5.2%;
  background: #ffffff;
  border-radius: 11px;
  
  @media all and (max-width: 1300px){
    .message{
      font-size: 14px !important;
          background: blueviolet;
    padding: 30px;
    color: white;
    text-align: center;
    font-size: 16px;
    }
    input[type='button']{
      height: 15% !important;
    }
  }
  
  .message{
    padding: 5px;
    color: white;
    font-size: 18px;
    line-height: 20px;
    text-align: justify;
    
  }
    
  input[type='button']{
    position: absolute;
    top: 100%;
    left: 50%;
    width: 50%;
    height: 36px;
    margin-top: -45px;
    margin-left: -25%;
    outline: 0;
    border: 0;
    background: #04B486;
    color: white;
    &:hover{
      transition: 0.3s;
      cursor: pointer;
    	background: lighten(#04B486, 5%);  
    }
  } 
}
      
.rab{
  width: 200px;
  height: 30px;
  outline: 0;
  border: 0;
  color: white;
  background: #04B486;
}
      
@keyframes fadeOut{
	from{
  	opacity: 1;
  } 
  to{
    opacity: 0;
  }
}
    
@keyframes fadeIn{
	from{
  	opacity: 0;
  } 
  to{
    opacity: 1;
  }
}
</style>
 <script>
	 var currentCallback;

// override default browser alert
window.alert = function(msg, callback){
  $('.message').text(msg);
  $('.customAlert').css('animation', 'fadeIn 0.3s linear');
  $('.customAlert').css('display', 'inline');
  setTimeout(function(){
    $('.customAlert').css('animation', 'none');
  }, 300);
  currentCallback = callback;
}

$(function(){
  
  // add listener for when our confirmation button is clicked
	$('.confirmButton').click(function(){
		window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp';
    $('.customAlert').css('animation', 'fadeOut 0.3s linear');
    setTimeout(function(){
     $('.customAlert').css('animation', 'none');
		$('.customAlert').css('display', 'none');
    }, 300);
    currentCallback();
  })
  $('.cancelButton').click(function(){
		//window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp';
    $('.customAlert').css('animation', 'fadeOut 0.3s linear');
    setTimeout(function(){
     $('.customAlert').css('animation', 'none');
		$('.customAlert').css('display', 'none');
    }, 300);
    currentCallback();
  })

  
  $('.rab').click(function(){
/*
    alert("Attendance save  Click OK to return to the main page,", function(){	    
      console.log("Callback executed");
    
      window.top.location='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp';
    })
*/
    
  });
  
/*
  // our custom alert box
  setTimeout(function(){
    alert('Test Me ', function(){
        console.log("Callback executed");
      });
  }, 500);
*/
});
	 </script>
	 
	 
	 <script src="alert/dist/sweetalert.min.js"></script>
  <link rel="stylesheet" href="alert/dist/sweetalert.css">
</head>

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
	<?php $connection = dbConnect();
		$date  = $_POST['date'];
		mysql_select_db("AttendanceTracking");
		$alert_query = mysql_query("SELECT id FROM attendance WHERE courseStar='".$_GET['starnumber']."'  AND attendedDate='".$date."' ", $connection);
	$num_rows1  = mysql_num_rows($alert_query); 
	?>
			<div class="well padding_2">
				<form id = "attendance_quick_form" method = "post" action = "">
				<input type="hidden" name="star" id="star" value="<?php echo $star_number; ?>">
					<input type="hidden" name="stuList" id="stuList" value="<?php echo $stuList; ?>">

				<?php
					if($operation === "update")
					{
						proc();
				?>
						<input type="submit" name="Submit" id="Submit" value="Save" class="btn btn-success"/>
<!-- 						<input type="submit" name="Submit" id="Submit" value="Save and Enter Another" class="btn btn-success"/> -->
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
				<a href = "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp" class = "btn btn-success">Back</a> <br>
				<br>
			
					<label for = "from-input">Date : </label>
					<input type = "text" id = "date"  name = "date" placeholder = "Click here to select dates">
				
					<div class="table-responsive">          
						<table id = "attendance_quick_table" class="table">
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
									$studNameArray = explode(",", $values[0]);
									$studName = $studNameArray[0];
									if( $values[5] == "Y" )
									{
										$left_early = "checked";
									}
									
									if ( $values[5] == "N" )
									{
										$left_early = "";
									}
									$present_checked = $absent_checked = $excused_checked = $tardy_checked = "";
									if($values[4] == "present")
										$present_checked = "checked";
									
									if($values[4] == "absent")
										$absent_checked = "checked";
									
									if($values[4] == "excused")
										$excused_checked = "checked";
									
									if($values[4] == "tardy")
										$tardy_checked = "checked";
							?>
									<tr>
										<td><?php echo $values[0]; 
											
												if(isset($values[3]))
												{
											?>
													<br><img src="https://www.wiu.edu/citr/AttendanceTracking/images/star.png">Notes: <a href="<?php echo $values[1]; ?>" target = "_blank"><?php echo $values[3]; ?></a>
											<?php 
												}
											?>
											<br><a href="<?php echo $values[2]; ?>" target = "_blank">Referrals</a>
										</td>
										<td><input id = "stud_pr_<?php echo $studName ."_". $i; ?>" checked="checked" value="present" name="student<?php echo $i; ?>" <?php echo $present_checked; ?> type="radio"><label for = "stud_pr_<?php echo $studName ."_". $i; ?>" ><span></span></label></td>
										<td><input id = "stud_ab_<?php echo $studName ."_". $i; ?>" value="absent" name="student<?php echo $i; ?>" <?php echo $absent_checked; ?> type="radio"><label for = "stud_ab_<?php echo $studName ."_". $i; ?>" ><span></span></label></td>
										<td><input id = "stud_ea_<?php echo $studName ."_". $i; ?>" value="excused" name="student<?php echo $i; ?>" <?php echo $excused_checked; ?> type="radio"><label for = "stud_ea_<?php echo $studName ."_". $i; ?>" ><span></span></label></td>
										<td><input id = "stud_tr_<?php echo $studName ."_". $i; ?>" value="tardy" name="student<?php echo $i; ?>" <?php echo $tardy_checked; ?> type="radio"><label for = "stud_tr_<?php echo $studName ."_". $i; ?>" ><span></span></label></td>
										<td><input id = "stud_le_<?php echo $studName ."_". $i; ?>" <?php echo $left_early; ?> value="LeftEarly" type="checkbox"><label for = "stud_le_<?php echo $studName ."_". $i; ?>" ></label></td>
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
							proc();
					?>
							<input type="submit" name="Submit" id="Submit" value="Save" class="btn btn-success"/>
<!-- 							<input type="submit" name="Submit" id="Submit" value="Save and Enter Another" class="btn btn-success"/> -->
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
		
		<div class='customAlert'>
  <p class='message' style="padding: 20px;
    background: rebeccapurple;
    color: white;
    font-size: 18px;
    text-align: center;
}"></p>
<!-- 	<input type='button' class='confirmButton' value='Ok'> -->

    <div class="col-sm-8">
    <input type='submit' name="CANCEL" id="CANCEL" class='cancelButton' style="font-size: 22px;
    background: #663399;
    color: white;
    border-radius: 7px;
    border: 0px;
    
    padding-left: 19px;
    padding-right: 19px;
    padding-top: 9px;
    padding-bottom: 9px;
    margin-top: 40px;"
    value='Save and Enter Another'>
    </div>
<div class="col-sm-4">
	<input type='submit' name="OK" id="OK" class='confirmButton' style="font-size: 22px;
    background: #663399;
    color: white;
    border-radius: 7px;
    border: 0px;
    
    padding-left: 19px;
    padding-right: 19px;
    padding-top: 9px;
    padding-bottom: 9px;
    margin: 40px;"
    value='OK'>
</div>

</div>
	<!-- /.container -->
	<?php 
 if($num_rows1>0 && isset($_POST['Submit'])){
echo '<script language="javascript">';
echo 'confirm("Quick Attendance for '.$date.' is already entered. Please Select Another Date.")';
echo '</script>';
}
else if($num_rows1<1 && isset($_POST['Submit']))
{
	echo '<script language="javascript">';
echo 'alert(" Attendance Save for '.$date.' . You are Redirecting to Main Page.")';
echo '</script>';

}
?>
<?php 
	include('footer.php');
?>