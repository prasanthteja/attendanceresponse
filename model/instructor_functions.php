<?php
	session_start();
	require_once('/home/mifdo/include/php/utilities/utility.sphp');
// ****************************************  FUNCTIONS ****************************************
	
	//getLdapInfo() starts
	function getLdapInfo($uid, &$name, &$wiuID, &$courseInfo, &$mail, &$uid, &$uidNumber, $nextTerm) 
	{
		require("/home/mifdo/https-files/php_ldap_bind.inc");
		$ds=ldap_connect("ldap.wiu.edu:389");  // must be a valid LDAP server!
		if (!$ds)
			return "Unable to connect to the LDAP server.  Contact the UCSS Helpdesk at 298-2704 for support.";
        $r=ldap_bind($ds,$BindDN,$BindPW);
		if ($r==0)
			return "Unable to bind to the LDAP server with the specified username and password.  Contact the UCSS Helpdesk at 298-2704 for support.";
		$criteria = "uid=". $uid;

		// Search uid entry
		$sr=ldap_search($ds, "ou=people,dc=wiu,dc=edu", $criteria);
		if($sr==0)
			return "Unable to find your username in LDAP.  Please try again or call the UCSS Helpdesk at 298-2704 if you continue to have trouble.";

		$userInfo = ldap_get_entries($ds, $sr);
		for($i=0; $i<$userInfo["count"]; $i++) 
		{
			$name = mysql_real_escape_string($userInfo[$i]["cn"][0]);
			$mail = $userInfo[$i]["mail"][0];
			$uid = $userInfo[$i]["uid"][0];
			$uidNumber = $userInfo[$i]["uidnumber"][0];
			$wiuID =  $userInfo[$i]["wiuid"][0];
		}
		
		if(!isset($_SESSION['email']))
			$_SESSION['email'] = $mail;
		if(!isset($_SESSION['name']))
			$_SESSION['name'] = $name;
			
        $today = date("Ymd");
        $current_date = date("Ym");	 
	    
	    //added by Roger for Brian Powell to deal with a presession class
	    if ($_SESSION['ecom']=="bkp104") 
		{
			switch (substr($current_date, 4, 2)) 
			{
				case "01":
				case "02":
				case "03":
				case "04":
					$current_term = "01";
					break;
				case "05":
				case "06":
				case "07":
					$current_term = "06";
					break;
				case "08":
				case "09":
				case "10":
				case "11":
				case "12":
					$current_term = "08";
					break;
			}   
	    } 
		else 
	    {
			switch (substr($current_date, 4, 2)) 
		    {
				case "01":
				case "02":
				case "03":
				case "04":
				case "05":
					$current_term = "01";
					break;
				case "06":
				case "07":
					$current_term = "06";
					break;
				case "08":
				case "09":
				case "10":
				case "11":
				case "12":
					$current_term = "08";
					break;
		    } 
	    }
	    if($nextTerm) 
		{
	    	switch($current_term) 
			{
	    		case "01":
	    			$nextTerm = "06";
	    			$nextTermYear = date("Y");
	    			break;
	    		case "06":
	    			$nextTerm = "08";
	    			$nextTermYear = date("Y");
	    			break;
	    		case "08":
	    			$nextTerm = "01";
	    			$nextTermYear = intval(date("Y")) + 1;
	    			break;
	    	}
	    	$termSelected = "$nextTermYear"."$nextTerm";	
	    }
	    else
	    	$termSelected = substr($current_date, 0,4).$current_term;	  
	    	
	    //	$termSelected='201901';  		
	       
        $filter = "(&(wiuinstructorid=" . $wiuID . ")(wiuterm=" . $termSelected . "))";
        
        $sr2=ldap_search($ds, "ou=courses,dc=wiu,dc=edu", $filter);
        $courseInfo = ldap_get_entries($ds, $sr2);
		ldap_close($ds);
    } //getLdapInfo() ends
	
	
	//***************************** Get LDAP Course Info *******************************************
	//ldap_course() starts
	function ldap_course($star)
	{
		require("/home/mifdo/https-files/php_ldap_bind.inc");
		$ds=ldap_connect("ldap.wiu.edu:389");  // must be a valid LDAP server!
		if (!$ds)
			return "Unable to connect to the LDAP server.  Contact the UCSS Helpdesk at 298-2704 for support.";

		$r=ldap_bind($ds,$BindDN,$BindPW);
		if ($r==0)
			return "Unable to bind to the LDAP server with the specified username and password.  Contact the UCSS Helpdesk at 298-2704 for support.";

		$criteria = "wiustarnumber=". $star; 
		
		$courseSearch = ldap_search($ds, "ou=courses,dc=wiu,dc=edu", $criteria);
		$courseInfo = ldap_get_entries($ds, $courseSearch); 
		$numEntries = ldap_count_entries($ds, $courseSearch); 
		ldap_close($ds);
		
		if($numEntries > 0)
			return $courseInfo;
	}//ldap_course() ends
	
	//************************ LDAP function to get users information ******************************
	function userldap($ecom) {
	
		require("/home/mifdo/https-files/php_ldap_bind.inc");
        
        $ds=ldap_connect("ldap.wiu.edu:389");  // must be a valid LDAP server!
        if (!$ds)
			return "Unable to connect to the LDAP server.  Contact the UCSS Helpdesk at 298-2704 for support.";
        
		$r=ldap_bind($ds,$BindDN,$BindPW);
        if ($r==0)
			return "Unable to bind to the LDAP server with the specified username and password.  Contact the UCSS Helpdesk at 298-2704 for support.";
			
		$criteria = "uid=$ecom";		//"wiuregisteredstar=".$star;
				
        $sr = ldap_search($ds, "ou=people,dc=wiu,dc=edu", $criteria);
        if($sr == 0)
            return array("Unable to find any students registered for the course.  Please try again or call the UCSS Helpdesk at 298-2704 if you continue to have trouble.","Error");
		
		$numEntries = ldap_count_entries($ds, $sr);
		$rosterInfo = ldap_get_entries($ds, $sr);
		ldap_close($ds);
		
		if($numEntries > 0){
			return $rosterInfo;}
	}//userldap() ends
	
	
// ********************************** 	LDAP INFORMATION FOR Mail ****************************************	
	////ldap_wiuID() starts
	function ldap_wiuID($wiuid) 
	{		
		require("/home/mifdo/https-files/php_ldap_bind.inc");
		
		$ds=ldap_connect("ldap.wiu.edu:389");  // must be a valid LDAP server!
		if (!$ds)
			return "Unable to connect to the LDAP server.  Contact the UCSS Helpdesk at 298-2704 for support.";
		
		$r=ldap_bind($ds,$BindDN,$BindPW);
		if ($r==0)
			return "Unable to bind to the LDAP server with the specified username and password.  Contact the UCSS Helpdesk at 298-2704 for support.";
			
		$criteria = "uid=$wiuid";		//"wiuregisteredstar=".$star;
				
		$sr = ldap_search($ds, "ou=people,dc=wiu,dc=edu", $criteria);
		if($sr == 0)
			return array("Unable to find any students registered for the course.  Please try again or call the UCSS Helpdesk at 298-2704 if you continue to have trouble.","Error");
		
		$numEntries = ldap_count_entries($ds, $sr);
		$rosterInfo = ldap_get_entries($ds, $sr);
		ldap_close($ds);
		
		if($numEntries > 0)
			return $rosterInfo;
	}//ldap_wiuID() ends
	
	//***************************** Get LDAP Student Info *******************************************
	//getLDAPstudentList() starts
	function getLDAPstudentList($star) {
		require("/home/mifdo/https-files/php_ldap_bind.inc");
		$ds=ldap_connect("ldap.wiu.edu:389");  // must be a valid LDAP server!
		if (!$ds)
			return "Unable to connect to the LDAP server.  Contact the UCSS Helpdesk at 298-2704 for support.";
		
		$r=ldap_bind($ds,$BindDN,$BindPW);
		if ($r==0)
			return "Unable to bind to the LDAP server with the specified username and password.  Contact the UCSS Helpdesk at 298-2704 for support.";
			
		$criteria = "wiuregisteredstar=".$star;
				
		$sr = ldap_search($ds, "ou=people,dc=wiu,dc=edu", $criteria);
		if($sr == 0)
			return array("Unable to find any students registered for the course.  Please try again or call the UCSS Helpdesk at 298-2704 if you continue to have trouble.","Error");
		
		$numentries = ldap_count_entries($ds, $sr);
		$rosterInfo = ldap_get_entries($ds, $sr);
		ldap_close($ds);
		if($numentries >0)
			return $rosterInfo;
	}//getLDAPstudentList() ends
	
	function LDAP_Query ( $UID )
	{
		$LDAP=ldap_connect("ldap.wiu.edu");

		## $Filter = 'uid=' . $UID ;
		$Base = 'uid=' . $UID . ',ou=People,dc=wiu,dc=edu' ;
		$Filter = 'cn=*' ;
		$Attributes = array( 'cn', 'givenName', 'sn', 'department', 'telephonenumber', 'title', 'mail' );
		$Search = ldap_search( $LDAP, $Base, $Filter, $Attributes,
							   0, 1000, 30  );
		$Entries = ldap_get_entries( $LDAP, $Search );
		$Hits = ldap_count_entries( $LDAP, $Search );
		ldap_close( $LDAP );
		if ( $Hits == 1 ) 
		{
			$_POST[ Contact ] = $Entries[0]["cn"][0] ;
			if ( $_POST[ 'Contact' ] == '*' ) 
			{
				$_POST[ ' Contact' ] = $Entries[0]["givenName"][0].' '.$Entries[0]["sn"][0];
			}	
			$_POST[ Department ] = $Entries[0]["department"][0] ;
			$_POST[ title ] = $Entries[0]["title"][0] ;
			$_POST[ mymail ] = $Entries[0]["mail"][0] ;
			$_POST[ Phone ] = $Entries[0]["telephonenumber"][0] ;
		}
	}
	
	// ********************************** 	LDAP INFORMATION FOR Mail ****************************************		
	function ldap1($mail="") 
	{
		require("/home/mifdo/https-files/php_ldap_bind.inc");
		$LDAP = ldap_connect("ldap.wiu.edu:389");

		if (!$LDAP)
			return "Unable to connect to the LDAP server.  Contact the UCSS Helpdesk at 298-2704 for support.";

		$r=ldap_bind($LDAP,$BindDN,$BindPW);
		if ($r==0)
			return "Unable to bind to the LDAP server with the specified username and password.  Contact the UCSS Helpdesk at 298-2704 for support.";

		$searchResult = ldap_search($LDAP, "ou=People, dc=wiu, dc=edu","mail=$mail");
		if($searchResult == 0)
		{
			return "Unable to find any information for the Email id.  Please try again .";
		}
		else
		{
			$numEntries = ldap_count_entries($LDAP, $searchResult);
			$info = ldap_get_entries($LDAP, $searchResult);
		}
		ldap_close($LDAP);
		if($numEntries > 0)
			return $info;
	}
	
	/**** function term() starts ****/
	function term($term)
	{
		switch (substr($term, 4, 2)) 
		{
		    case "01":
			case "02":
			case "03":
			case "04":
			case "05":
				return("Spring ".substr($term, 0 , 4 ));
				break;
			case "06":
			case "07":
				return("Summer ".substr($term, 0 , 4));
				break;
			case "08":
			case "09":
			case "10":
			case "11":
			case "12":
				return("Fall".substr($term, 0 , 4));
				break;
		}
		echo "TermFound:";
	}//function term() ends
	
	/**** function courseListing starts ****/
	function courseListing() 
	{
		
		$ga_course_info = array();
		$connection=dbConnect();
		mysql_select_db("AttendanceTracking");
		
		$uid = $_SESSION['ecom'];
		$getRole = mysql_query("SELECT star FROM courseinfo WHERE GA = '" . $uid . "'",	$connection) or die (mysql_error());
		if(mysql_num_rows($getRole) > 0)
		{
			$row = mysql_fetch_array($getRole);
			$star_number = $row['star'];
			$role1="GA";
			// As instructor and GA
			if($_SESSION['role'] == "instructor" && $role1 == 'GA')
			{
				$ga_course_info = displayGADetails($uid, $star_number);
				$final_course_details_ga = $ga_course_info;
				
				$final_course_details_instructor = userCourseList($uid);
				$final_course_details = array_merge($final_course_details_ga, $final_course_details_instructor);
				require_once('view/ga_instructor.php');exit;
			}
			else
			{
				$ga_course_info = displayGADetails($uid, $star_number);
				$final_course_details = $ga_course_info;
				require_once('view/ga_course_listing.php');exit;
			}
		}	
		else
		{
			$final_course_details = userCourseList($uid);
			require_once('view/course_listing.php');
		}
			
	} // function courseListing() ends
	
	/**** function userCourseList() starts ****/
	function userCourseList($uid)
	{
		$connection = dbConnect();
		mysql_select_db("AttendanceTracking");
		
		$error = getLdapInfo($uid, $name, $wiuID, $courseInfo, $mail, $uid, $uidNumber, $nextTerm);
		if(!empty($error))
			return array($error,"Error Occurred");
		
		$course_details = array();
		$final_course_details = array();
		$x=0;
		if($uid=='mfdl')
		{
			
			$getInfo = mysql_query("SELECT * FROM courseinfo WHERE ecom = '" . $uid . "' and term='201901'",	$connection) or die (mysql_error());
			while($row = mysql_fetch_array($getInfo)){
		//	echo $row[1];
				$c_title = $row[1];
			$c_title = htmlentities($c_title, ENT_QUOTES);
			$c_enrollment = $courseInfo[$x]["wiucourseenrollment"][0];
			$c_star = $row[2];
			$c_section = $row[3];					
			$c_number = $row[4];	
			$c_days = $courseInfo[$x]["wiucoursedays"][0];
			$c_time = $courseInfo[$x]["wiucoursetime"][0]; 
			$c_start = $courseInfo[$x]["wiucoursestartdate"][0]; 
			$c_end = $courseInfo[$x]["wiucourseenddate"][0]; 
			$term = $courseInfo[$x]["wiuterm"][0]; 
			$department = $row[8];
			$course_details[$x] = array();
			array_push($course_details[$x], $c_title, $c_enrollment, $c_star, $c_section, $c_number, $c_days, $c_time, $term, $department, $newterm);
			$x++;

				}
				
		}
		else
		{
			
		
		//echo $courseInfo["count"];
		for($x = 0; $x < $courseInfo["count"]; $x++) 
		{
			//print_r($courseInfo[$x]);
			$c_title = $courseInfo[$x]["wiucoursetitle"][0];
			$c_title = htmlentities($c_title, ENT_QUOTES);
			$c_enrollment = $courseInfo[$x]["wiucourseenrollment"][0];
			$c_star = $courseInfo[$x]["wiustarnumber"][0];
			$c_section = $courseInfo[$x]["wiucoursesection"][0];						
			$c_number = $courseInfo[$x]["cn"][0];		
			$c_days = $courseInfo[$x]["wiucoursedays"][0];
			$c_time = $courseInfo[$x]["wiucoursetime"][0]; 
			$c_start = $courseInfo[$x]["wiucoursestartdate"][0]; 
			$c_end = $courseInfo[$x]["wiucourseenddate"][0]; 
			$term = $courseInfo[$x]["wiuterm"][0]; 
			$department = $courseInfo[$x]["wiucoursedepartment"][0];
			
			$department = htmlentities($department, ENT_QUOTES);
			
			//$newterm = term($term);
			$course_details[$x] = array();
			
			$querystar = mysql_query("select star from courseinfo where ((star = '$c_star'  ) and (term='$term')) ") ; 
			$num_rows = mysql_num_rows($querystar);
			
			if($num_rows > 0) 
			{}
			else
			{
				//$myquery=sprintf("INSERT INTO courseinfo VALUES('','%s', '%s', '%s','%s' , '%s', '%s','', '%s')",$c_title,$c_star, $c_section,$c_number, $term, $uid, $department);
				//echo $myquery;
				
				//mysql_query($myquery, $connection) or die(mysql_error());
				
				mysql_query("INSERT INTO courseinfo VALUES('','$c_title', '$c_star', '$c_section','$c_number' , '$term', '$uid','', '$department' )",$connection) or die(mysql_error());
				
			}
			array_push($course_details[$x], $c_title, $c_enrollment, $c_star, $c_section, $c_number, $c_days, $c_time, $term, $department, $newterm);
		}
		}
		$final_course_details['course_info'] = $course_details;
		return $final_course_details['course_info'];
	}//function userCourseList() ends
	
	
	
	/**** function displayGADetails() starts ****/
	function displayGADetails($uid, $star_number)
	{
		$course_details = array();
		$final_course_details = array();
		
		$querystar = "select star from courseinfo where GA='$uid'" ;
		
		$getprojectinfo = mysql_query($querystar);
		$num_rows = mysql_num_rows($getprojectinfo);
		$y = 0;
		if($num_rows > 0)
		{	
			while(list($star) = mysql_fetch_array($getprojectinfo))
			{
				$courseInfo = ldap_course($star);
				for($x = 0; $x < $courseInfo["count"]; $x++) 
				{		
					$c_title = $courseInfo[$x]["wiucoursetitle"][0];
					$c_title = htmlentities($c_title, ENT_QUOTES);
					$c_enrollment = $courseInfo[$x]["wiucourseenrollment"][0];
					$c_star = $courseInfo[$x]["wiustarnumber"][0];
					$c_section = $courseInfo[$x]["wiucoursesection"][0];						
					$c_number = $courseInfo[$x]["cn"][0];
					$c_days = $courseInfo[$x]["wiucoursedays"][0];
					$c_time = $courseInfo[$x]["wiucoursetime"][0]; 
					$c_start = $courseInfo[$x]["wiucoursestartdate"][0]; 
					$c_end = $courseInfo[$x]["wiucourseenddate"][0]; 
					$term = $courseInfo[$x]["wiuterm"][0]; 
					$department = $courseInfo[$x]["wiucoursedepartment"][0];
					$department = htmlentities($department, ENT_QUOTES);
					$newterm = term($term);
					$course_details[$y] = array();
					$gaYes = "GA";
					array_push($course_details[$y], $c_title, $c_enrollment, $c_star, $c_section, $c_number, $c_days, $c_time, $term, $department, $newterm, $gaYes);
					$y++;
				}
			}
		}
		return $course_details;
	} //function displayGADetails() ends
	
	/**** function chooseCourseQuick() starts ****/
	function chooseCourseQuick($star_number, $operation) 
	{
		$operation = $operation;
		$uid = $_SESSION['ecom'];
		$connection = dbConnect();
		mysql_select_db("AttendanceTracking");
		$query = mysql_query("SELECT ecom,GA FROM courseinfo 
											WHERE star='$star_number'
											AND (ecom='$uid' OR GA = '$uid')  /*Added by Harika Annabathina*/
											",$connection) or die("Error: ".mysql_error());
						
		$num_rows = mysql_num_rows($query); 
		if($num_rows > 0)
		{
			list($userecom,$GAecom) = mysql_fetch_array($query);
		}
		else
		{
			$content = "There is no course exists with ";
			$content .= $star_number;
			require_once('view/no_record.php');exit;
		}
		
		$rosterInfo = getLDAPstudentList($star_number);
    	
		if($rosterInfo["count"] > 0) 
		{
			$studentArray = array();
			for($i = 0; $i < $rosterInfo["count"]; $i++) 
			{
				$ecom = base64_encode($rosterInfo[$i]["uid"][0]);
				$ecom1 = $rosterInfo[$i]["uid"][0];
				$fn = $rosterInfo[$i]["givenname"][0];
				$ln = $rosterInfo[$i]["sn"][0];
				$rank=mysql_real_escape_string($rosterInfo[0]["wiuClassification"][0]);
				$uidNumber = $rosterInfo[$i]["uidnumber"][0];
				$email = '"'.$fn.' '.$ln.'" <'.htmlentities($rosterInfo[$i]["mail"][0]).'>,';
				$student = array($ln.", ".$fn,$uidNumber,$email,$ecom,$ecom1);
				array_push($studentArray,$student);
			}
			$stud_rec = 0;
			foreach($studentArray as $student)
			{  
				if(!empty($attendance_date))
				{
					$query = mysql_query("SELECT attendance FROM attendance 
												WHERE attendedDate='" . $attendance_date . "' AND studentEcom='" . $student[4] . "' AND courseStar='" . $star_number . "'
												",$connection) or die("Error: ".mysql_error());
					list($attend) = mysql_fetch_array($query);
					
					//var_dump($attend_);
					$query1 = mysql_query("	SELECT LeftEarlyflag  
									FROM LeftEarly INNER JOIN attendance 
									ON LeftEarly.id=attendance.id 
									WHERE attendedDate='".$attend."' AND studentEcom='".base64_decode($student[3])."' AND courseStar='".$star_number."'
						",$connection) or die ("update:  ".mysql_error());
					list($leftflag) = mysql_fetch_array($query1);
				}
				else
				{
					$uid = $_SESSION['ecom'];
					$getAttendance1 = mysql_query("SELECT preference FROM userpreference WHERE courseinfo='$star_number' and  ecom = '$uid'",$connection) or die(mysql_error());
					$rows1 = mysql_num_rows($getAttendance1);
					if($rows1 > 0) 
					{
						list($attend) = mysql_fetch_array($getAttendance1);
						$leftflag = "N";
					}
				}
				
				$date1=date('Y-m-d');	
				$getnote1 =	mysql_query(" SELECT note,DATE_FORMAT(date, '%m-%d')as date1,date  FROM notes WHERE  studentEcom='$student[4]' AND courseStar='$star_number'",$connection) or die(mysql_error());
				$name= $student[0];
						$ecom= $student[4];
						$star1=$star_number;
				
				list($note,$formatteddate,$date) = mysql_fetch_array($getnote1);
				$stud_rec_array[$stud_rec] = array();
				$origdate = $date;
				$stud_name = $student[0];
				$stuList .= $student[3]."|";
				$Notes = 'view/view_notes.php?student=' . $name . '&stuEcom=' . $ecom . '&date=' . $origdate . '&star=' . $star_number;
				$noteTitle = $formatteddate;
				$referrals = 'https://www.wiu.edu/citr/AttendanceTracking/functions/referralsForm.sphp?studentName='. $student[0] .'&amp;studentecom='.$ecom.'&amp;userid=' . $_SESSION["ecom"];
				array_push($stud_rec_array[$stud_rec], $stud_name, $Notes, $referrals, $noteTitle, $attend, $leftflag);
				$stud_rec++;
			}
			require_once('view/quick_attendance.php');
		}
		else
		{
			require_once('view/no_record.php');
		}
	}
	/**** function chooseCourseQuick() ends ****/
	
	/**** function selectCourse() starts ****/
	function selectCourse($star_number, $operation, $date) 
	{
		//echo  $star_number.'/'.$operation.'/'.$date.'<br/>';
		//exit;
		$operation = $operation;
		$attendance_date = $date;
		$uid = $_SESSION['ecom'];
		//echo $uid.'<br/>';
		//exit;
		$connection = dbConnect();
		mysql_select_db("AttendanceTracking");
		$query = mysql_query("SELECT ecom,GA FROM courseinfo 
											WHERE star='$star_number'
											AND (ecom='$uid' OR GA = '$uid')  /*Added by Harika Annabathina*/
											",$connection) or die("Error: ".mysql_error());
						
		$num_rows = mysql_num_rows($query); 
		if($num_rows > 0)
		{ /*echo json_encode($userecom).'<br/>';
			echo json_encode($gAecom).'<br/>';*/
			list($userecom,$GAecom) = mysql_fetch_array($query);
		}
		else
		{
			$content = "There is no course exists with ";
			$content .= $star_number;
			require_once('view/no_record.php');exit;
		}
		$rosterInfo = getLDAPstudentList($star_number);
    	if($rosterInfo["count"] > 0) 
		{
			$studentArray = array();
			for($i = 0; $i < $rosterInfo["count"]; $i++) 
			{
				$ecom = base64_encode($rosterInfo[$i]["uid"][0]);
				$ecom1 = $rosterInfo[$i]["uid"][0];
				$fn = str_replace("'","",$rosterInfo[$i]["givenname"][0]);
				$ln = str_replace("'","",$rosterInfo[$i]["sn"][0]);
				$uidNumber = $rosterInfo[$i]["uidnumber"][0];
				$email = '"'.$fn.' '.$ln.'" <'.htmlentities($rosterInfo[$i]["mail"][0]).'>,';
				$wiuID =  $rosterInfo[$i]["wiuid"][0];
				$mail = $rosterInfo[$i]["mail"][0];
				$mobile = $rosterInfo[$i]["mobile"][0];
				$student = array($ln.", ".$fn, $uidNumber, $email, $ecom, $ecom1, $wiuID, $mail, $mobile);
				array_push($studentArray, $student);
			}
			$stud_rec = 0;
			sort($studentArray);
			foreach($studentArray as $student)
			{  
				$date1=date('Y-m-d');	
				$getnote1 =	mysql_query(" SELECT note,DATE_FORMAT(date, '%m-%d')as date1,date  FROM notes WHERE  studentEcom='$student[4]' AND courseStar='$star_number'",$connection) or die(mysql_error());
				$name= $student[0];
				$ecom= $student[4];
				$star1=$star_number;
				$uid = $student[1];
				$studentPicture = getPicture($student[5], $student[6]);
				list($note,$formatteddate,$date) = mysql_fetch_array($getnote1);
				$stud_rec_array[$stud_rec] = array();
				$origdate = $date;
				$stud_name = $student[0];
				$stuList .= $student[3]."|";
				$Notes = 'view/view_notes.php?student=' . $name . '&stuEcom=' . $ecom . '&date=' . $origdate . '&star=' . $star_number;
				$email = 'view/emaillightbox.php?student=' . $name . '&stuEcom=' . $ecom . '&star=' . $star_number;
				$noteTitle = $formatteddate;
				$referrals = 'https://www.wiu.edu/citr/AttendanceTracking/functions/referralsForm.sphp?studentName='. $student[0] .'&amp;studentecom='.$ecom.'&amp;userid=' . $_SESSION["ecom"];
				$progressBar = 'Attendance Progress';
				$mobile = $student[7];
				if(empty($attendance_date))
				{
					$attendance_date=date("Y-m-d");
				}
				
				if(!empty($attendance_date))
				{
					$query = mysql_query("SELECT attendance FROM attendance 
												WHERE attendedDate='" . $attendance_date . "' AND studentEcom='" . $student[4] . "' AND courseStar='" . $star_number . "'
												",$connection) or die("Error: ".mysql_error());
					list($attend) = mysql_fetch_array($query);
					
					//var_dump($attend_);
					$query1 = mysql_query("	SELECT LeftEarlyflag  
									FROM LeftEarly INNER JOIN attendance 
									ON LeftEarly.id=attendance.id 
									WHERE attendedDate='".$attend."' AND studentEcom='".base64_decode($student[3])."' AND courseStar='".$star_number."'
						",$connection) or die ("update:  ".mysql_error());
					list($leftflag) = mysql_fetch_array($query1);
				}
				else
				{
					$uid = $_SESSION['ecom'];
					$getAttendance1 = mysql_query("SELECT preference FROM userpreference WHERE courseinfo='$star_number' and  ecom = '$uid'",$connection) or die(mysql_error());
					$rows1 = mysql_num_rows($getAttendance1);
					if($rows1 > 0) 
					{
						list($attend) = mysql_fetch_array($getAttendance1);
						$leftflag = "N";
					}
					
				}
				$getPresentCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='present' AND studentEcom='$student[4]' AND courseStar='$star_number' 
											",$connection)) or die(mysql_error());
				$getAbsentCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='absent' AND studentEcom='$student[4]' AND courseStar='$star_number'
										",$connection)) or die(mysql_error());
				$getExcusedCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='excused' AND studentEcom='$student[4]' AND courseStar='$star_number'
										",$connection)) or die(mysql_error());
				$getTardyCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='tardy' AND studentEcom='$student[4]' AND courseStar='$star_number'
										",$connection)) or die(mysql_error());
				$getLeftEarlyCount = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM LeftEarly INNER JOIN attendance ON LeftEarly.id=attendance.id WHERE LeftEarlyflag='Y' AND studentEcom='$student[4]' AND courseStar='$star_number'
										",$connection)) or die(mysql_error());
				array_push($stud_rec_array[$stud_rec], $stud_name, $Notes, $referrals, $noteTitle, $progressBar, $studentPicture, $email, $getPresentCount[0], $getAbsentCount[0], $getExcusedCount[0], $getTardyCount[0], $getLeftEarlyCount[0], $attend, $leftflag, $uid, $mobile, $ecom);
				
				$stud_rec++;
			}
			//echo $date;
			//require_once('view/full_featured_dd.php'); //with DropDown option
			require_once('view/full_featured.php');
		}
		else
		{
			require_once('view/no_record.php');
		}
	}
	/**** function selectCourse() ends ****/
	
	/**** function editAttendance() ends ****/
	function editAttendance($star_number)
	{
		$attendance_date_array = array();
		$dateQuery = mysql_query("SELECT DISTINCT id, attendedDate FROM attendance Where courseStar = '$star_number' GROUP BY attendedDate ORDER BY attendedDate ASC");
		if(mysql_num_rows($dateQuery) > 0 )
		{
			while(list($id, $date) = mysql_fetch_array($dateQuery)) 
			{	
				array_push ($attendance_date_array, $date); 
			}	
		}
		require_once('view/edit_attendance.php');
	}
	/**** function editAttendance() ends ****/
	
	
	/**** function process() starts ****/
	function process($opt) 
	{	
		switch($opt) 
		{
			case "Save":
				return processSave();
				
			case "Save and Enter Another":
				return processSave("continue");	
				
			case "Save Changes":
				return processSave("edit");	
				
			case "Remove All Records for This Day":
				return processDelete();
			
			case "Save GA":
				return saveGA($_POST['star']);
				
			case "Save Note":
				return saveNote();
		}
	}
	/**** function process() ends ****/
	
	function processSave($option = "")
	{
		$connection = dbConnect();
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
		if ($option == "continue")
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
		if(empty($action))
		{
			require_once('view/attendance_updated.php');
		}
		else
		{
			require_once("view/redirect.php");
		}
	} //processSave() ends

	//processDelete() starts
	function processDelete()
	{
		$uid1 = $_SESSION['ecom'];
		$star = $_POST['star'];
		$classdate = $_POST['date'];
		$stuList = explode('|',$_POST['stuList']);

		$connection = dbConnect();
		mysql_select_db("AttendanceTracking");

		$querystar = "select ecom from courseinfo where GA='$uid1' and star='$star'" ; 
		$getproject = mysql_query($querystar, $connection) or die(mysql_error());
		if (mysql_num_rows($getproject) > 0)
		{
			while(list($insid) = mysql_fetch_array($getproject))
			{
				$intructorInfo = userldap($insid);
				$instructormail = $intructorInfo[0]["mail"][0];
			}	
		}

		$LDAP = ldap_connect("ldap.wiu.edu");
		$result = ldap_bind($LDAP);

		$searchResult = ldap_search($LDAP, "ou=People, dc=wiu, dc=edu", "uid=".$_SESSION['ecom']);

		$numEntries = ldap_count_entries($LDAP, $searchResult);

		$info = ldap_get_entries($LDAP, $searchResult);

		ldap_close($LDAP);

		$cn= $info[0]["cn"][0];
		$fna = $info[0]["givenname"][0];
		$lna = $info[0]["sn"][0];
		$title = $fna.' '.$lna;
		$mailz = $info[0]["mail"][0];
		$uid1=$info[0]["uid"][0];
		$courseInfo = ldap_course($star);
		$course_title = $courseInfo[0]["cn"][0];
		$course_section = $courseInfo[0]["wiucoursesection"][0];
		$headers = "From: CITR@wiu.edu" . "\r\n" .
		"CC: $instructormail";

		$email_to="$mailz";
		
		$email_subject="Attendance Backup ";
		$msg= " 

		$fna $lna,

		your attendance for $course_title - $course_section was removed for $classdate in CITR's Attendance Tracker. The following is provided to you as a backup of the deleted information.

		"; 

		$getDetails=mysql_query("SELECT studentname,attendance,LeftEarlyflag 
		FROM LeftEarly INNER JOIN attendance 
		ON LeftEarly.id=attendance.id 
		WHERE courseStar='$star' AND attendedDate='$classdate'"
		,$connection)    or die(mysql_error());
		$x = 0;
		while($row = mysql_fetch_array($getDetails))
		{
			$msg .="\r\n".$row['studentname']."    -   "  .$row['attendance'];
			if($row['LeftEarlyflag']=="Y")
			{
				$msg .=", Left Early";
			}
			$x = $x +1; 
		}
		$msg .= "\r\n\n Thank you."; 
		$msg = wordwrap($msg,70);
		
		$query=mysql_query("DELETE FROM attendance WHERE courseStar='$star' AND attendedDate='$classdate'",$connection)    or die(mysql_error());
		
		if(mysql_affected_rows() > 0 )
		{
			$st = mysql_query("Select id FROM attendance WHERE courseStar='$star' AND attendedDate='$classdate'",$connection)    or die(mysql_error());
			while($row = mysql_fetch_array($getDetails))
			{
				if($row['LeftEarlyflag']=="Y" || $row['LeftEarlyflag']=="N")
				{
					$query1=mysql_query("DELETE FROM LeftEarly   
										WHERE id=(Select id From attendance WHERE courseStar='$star' AND attendedDate='$classdate')
										",$connection) or die ("delete:  ".mysql_error());
				}
			}
			$msg = "The attendance was deleted successfully and a copy of the removed records has been sent to the instructor.";
		}
		else 
		{
			$msg = "There was an error deleting the attendance.  Please try again.";
		}
		mysql_close($connection);
		if(empty($action))
		{
			require_once('view/attendance_updated.php');
		}
		else
		{
			require_once("view/redirect.php");
		}
	}//processDelete() ends
	
	
	// ************* Email Student Reports  *************//
	function emailStudentReport($star)
	{
		$LDAP = ldap_connect("ldap.wiu.edu");
		$result = ldap_bind($LDAP);
		
		$searchResult = ldap_search($LDAP, "ou=People, dc=wiu, dc=edu", "uid=".$_SESSION['ecom']);
		
		$numEntries = ldap_count_entries($LDAP, $searchResult);
		
		$info = ldap_get_entries($LDAP, $searchResult);
		
		ldap_close($LDAP);
		
		if($numEntries > 0)
			$fna = $info[0]["givenname"][0];
			$lna = $info[0]["sn"][0];
			$title = $fna.' '.$lna;
			$mailz = $info[0]["mail"][0];
			
			$connection = dbConnect();
			mysql_select_db("AttendanceTracking");
			
			$subjectId=$star;
			$rosterInfo = getLDAPstudentList($star);
			$studentInfo = userldap($uid);
			
			$courseInfo = ldap_course($star);
			$course_title = $courseInfo[0]["cn"][0];
			$course_section = $courseInfo[0]["wiucoursesection"][0];
		
			$getdeta= mysql_query("SELECT 
												studentEcom,COUNT(studentEcom) as number
										FROM 
											attendance 
										WHERE 
											courseStar='$star' 
										GROUP BY 
											studentEcom 
										",$connection) or die(mysql_error());
			
			$getAttendance = mysql_query("SELECT 
												studentEcom,attendance,attendedDate,COUNT(studentEcom) as number
										FROM 
											attendance 
										WHERE 
											courseStar='$star' 
										GROUP BY 
											studentEcom 
										",$connection) or die(mysql_error());
			if($rosterInfo["count"] > 0) 
			{
				for($i=0;$i<$rosterInfo["count"];$i++)
				{
					$ecom = base64_encode($rosterInfo[$i]["uid"][0]);
					$fname = $rosterInfo[$i]["givenname"][0];
					$lname = $rosterInfo[$i]["sn"][0];
					$uidNumber = $rosterInfo[$i]["uidnumber"][0];
					$email = $rosterInfo[$i]["mail"][0];
					$uid = $rosterInfo[$i]["uid"][0];

					$result = mysql_fetch_array($getAttendance);
					$getPresentCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='present' AND studentEcom='$uid' AND courseStar='$star' 
											",$connection)) or die(mysql_error());
					$getAbsentCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='absent' AND studentEcom='$uid' AND courseStar='$star'
											",$connection)) or die(mysql_error());
					$getExcusedCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='excused' AND studentEcom='$uid' AND courseStar='$star'
											",$connection)) or die(mysql_error());
					$getTardyCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='tardy' AND studentEcom='$uid' AND courseStar='$star'
											",$connection)) or die(mysql_error());
					$getLeftEarlyCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM LeftEarly INNER JOIN attendance ON LeftEarly.id=attendance.id WHERE LeftEarlyflag='Y' AND studentEcom='$uid' AND courseStar='$star'",$connection)) or die(mysql_error());
					
					$newquery = mysql_query("SELECT 
												 id,attendedDate, attendance 
											FROM 
												attendance 
											WHERE 
												studentEcom='$uid' 
											AND 
												courseStar='$subjectId' 
											ORDER BY 
												attendedDate 
											",$connection) or die(mysql_error());
											
					$dispDate = date("Y-m-d");
					
					$email_from="FROM : $mailz ";//. "\r\n" .
					$email_to="$email";
					$email_subject="Attendance Results";
					$msg= " 
					
	$fname $lname,
			
	Your attendance details for $course_title-$course_section, are contained in this email and summarize the attendance entered by your instructor up to $dispDate.
		Totals: 
		Present: $getPresentCount[0] days.
		Absent : $getAbsentCount[0] days.
		Excused: $getExcusedCount[0] days.
		Tardy  : $getTardyCount[0] days. 
		Left Early: $getLeftEarlyCount[0] days.
			
		Details :"; 
				
			$x = 0;
			while(list($id,$attendedDate,$attendance) = mysql_fetch_array($newquery))
			{
				$msg .="\r\n\t".$attendedDate."~" .$attendance;
				$query1=mysql_query("SELECT 
									LeftEarlyflag  
									FROM 
									LeftEarly 
									WHERE 
									id='$id'  
									",$connection) or die(mysql_error());
				list($left) = mysql_fetch_array($query1);
				if($left=="Y")
				{
					$msg.=", left early.";
				}						
				else
				{
					$msg.=".";
				}		
				$x = $x +1; 
			}
				$msg .= "\r\n\n Thank you."; 
				$msg = wordwrap($msg,70);
				mail($email_to, $email_subject, $msg, $email_from);
			}	
		}
		$msg = "";
		$msg = "The emails have been sent to the Students.";
		require_once('view/attendance_updated.php');
		mysql_close();
	}
	
	//*************FUNCTION FOR ADD GA **********
	//addGA() starts
	function addGA($star)
	{
		$uid=$_SESSION['ecom'];
		$connection=dbConnect();
		mysql_select_db("AttendanceTracking");
		$query = mysql_query("SELECT ecom FROM courseinfo 
											WHERE star='$star'
											AND (ecom='$uid' OR GA = '$uid')   /*Added by Harika Annabathina*/
											",$connection) or die("Error: ".mysql_error());
						
		$num_rows = mysql_num_rows($query); 
			if($num_rows > 0)
			{
				list($userecom) = mysql_fetch_array($query);
			}
			else
			{
				$content="There is no course exists with ";
				$content .=$star;
				return array ($content,	"ADD GA");
			}
		if($userecom==$uid)
		{
			$getGA = mysql_query("SELECT GA FROM courseinfo WHERE star= '$star'",
								 $connection) or die (mysql_error());
			if(mysql_num_rows($getGA) > 0)
			{
				list($GAid) = mysql_fetch_array($getGA);
			}
			$recall = LDAP_Query($GAid);
			$email =  $_POST[ 'mymail' ];
			header('location: https://www.wiu.edu/citr/AttendanceResponsive/view/save_ga.php?starnumber=' . $star . '&email=' . $email);
		}
		else
		{
			header('location: https://www.wiu.edu/citr/AttendanceResponsive/view/not_authorised.php?starnumber=' . $star);
		}
	}//addGA() ends
	
	
	//*************FUNCTION FOR SAVE GA ************
	//saveGA() starts
	function saveGA($star)
	{
		$cid = $star;
		$connection=dbConnect();
		mysql_select_db("AttendanceTracking");
		$email = mysql_real_escape_string(fixSmartQuotes($_POST['ga_mail']));
		$info = ldap1($email);	
		$GAid= $info[0]["uid"][0];
		$query = "select GA from courseinfo WHERE GA = '$GAid' and star = '$cid'";										
		$queryresult = mysql_query($query,$connection) or die (mysql_error());
		if(mysql_num_rows($queryresult) > 0) 
		{
			$msg="GA was already added with this Email.";
		}
		else if($GAid==NULL)
		{
			$msg="";
			$update = "UPDATE courseinfo SET GA='' WHERE star = '$cid'";
			$result = mysql_query($update,$connection) or die (mysql_error());
			if(mysql_affected_rows($connection) === 1) 
			{
				$msg = "GA has been removed.";
			}
			else 
			{
				$msg = "You must use a valid WIU address to use this feature.";
			}	
		}
		else
		{
			$update = "UPDATE courseinfo SET GA='$GAid' WHERE star = '$cid'";										
			$result = mysql_query($update,$connection) or die (mysql_error());
			if(mysql_affected_rows($connection) === 1) 
			{
				$msg = "The GA was successfully Added.";
			}
			else 
			{
				$msg = "There was an error adding the GA.  Please try again.";
			}		
		}
		mysql_close($connection);
		require_once('view/ga_added.php');
	}//saveGA() ends
	
	//******FUNCTION FOR ADD NOTE************
	//addNote() starts
	function addNote($star)
	{
		$uid=$_SESSION['ecom'];
		$connection=dbConnect();
		mysql_select_db("AttendanceTracking");
		//Added by Harika Annabathina
		$query = mysql_query("SELECT ecom,GA FROM courseinfo 
											WHERE star='$star'
											AND (ecom='$uid' OR GA = '$uid')
											",$connection) or die("Error: ".mysql_error());
						
		$num_rows = mysql_num_rows($query); 
		if($num_rows > 0)
		{
			
			list($userecom,$GAecom) = mysql_fetch_array($query);
		}
		else
		{
			
			$content = "There is no course exists with ";
			$content .= $star_number;
			require_once('view/no_record.php');exit;
		}
		$courseInfo = ldap_course($star);
		$rosterInfo = getLDAPstudentList($star);
		$datevalue = date('Y-m-d');
		if($rosterInfo["count"] > 0) 
		{
			$groupList = '';
			$studentArray = array();
			
			for($i = 0; $i < $rosterInfo["count"]; $i++) 
			{
				$ecom = base64_encode($rosterInfo[$i]["uid"][0]);
				$ecom1 = $rosterInfo[$i]["uid"][0];
				$fn = $rosterInfo[$i]["givenname"][0];
				$ln = $rosterInfo[$i]["sn"][0];
				$uidNumber = $rosterInfo[$i]["uidnumber"][0];
				$email = '"'.$fn.' '.$ln.'" <'.htmlentities($rosterInfo[$i]["mail"][0]).'>,';
				$wiuID =  $rosterInfo[$i]["wiuid"][0];
				$mail = $rosterInfo[$i]["mail"][0];
				$student = array($ln.", ".$fn,$uidNumber,$email,$ecom,$wiuID, $mail,$ecom1);
				array_push($studentArray,$student);
			}  
			sort($studentArray); 
			$i = 0;
			
			foreach($studentArray as $student)
			{  
				$name = $student[0];
				$ecom= $student[6];
				$getecom = mysql_query(" SELECT studentEcom FROM attendance WHERE  studentEcom='$student[6]'",$connection) or die(mysql_error());
				if (mysql_num_rows($getecom) > 0)
				{
					$stud_details[$i] = array();
					list($stuEcom) = mysql_fetch_array($getecom);
					array_push($stud_details[$i], $name, $stuEcom);
					$i++;
				}
			}
			
			require_once('view/add_note.php');
		}
		else
		{
			
			require_once('view/no_record.php');
		}
	}//addNote() ends
	//************* FUNCTION FOR SAVE Note ***************
	//saveNote() starts
	function saveNote()
	{
		$stuEcom=$_POST['stuName'];;
		$text=mysql_real_escape_string($_POST['note']);
		$date = $_POST['note_date'];
		$star=$_POST['star'];

		$studentInfo = userldap($stuEcom);
		$recall = LDAP_Query ($stuEcom);
		$connection=dbConnect();
		mysql_select_db("AttendanceTracking");

		$insertnote = "INSERT INTO notes VALUES ('', '$text', '$stuEcom', '$star', '$date' )";

		$result = mysql_query($insertnote,$connection) or die (mysql_error());

		if(mysql_affected_rows($connection) == 1) 
		{
			$msg = "The note was successfully added.";
		}
		else
		{
			$msg = "There was an error adding the note.  Please try again.";
		}
		require_once('view/note_added.php');
	}//saveNote() ends
	
	
	/* ************************* EXPORT ATTENDANCE ****************************************** */
	//exportAttendance() starts
	function exportAttendance($star)
	{
		$uid=$_SESSION['ecom'];
		$connection=dbConnect();
		mysql_select_db("AttendanceTracking");
		$query = mysql_query("SELECT ecom,GA FROM courseinfo WHERE star='$star'",$connection) or die("Error: ".mysql_error());

		$num_rows = mysql_num_rows($query); 
		if($num_rows > 0)
		{
			list($userecom,$GAecom) = mysql_fetch_array($query);
		}
		else
		{
			$content="There is no course exists with ";
			$content .=$star;
		}

		$filename = 'ExportAttendance.csv';

		$csv_terminated = "\n";
		$csv_separator = ",";
		$csv_enclosed = '"';
		$csv_escaped = "\\";


		$sql_query = "SELECT distinct StudentName, attendance.courseStar, attendance,attendedDate,  LeftEarlyflag FROM attendance LEFT JOIN LeftEarly ON attendance.id=LeftEarly.id WHERE attendance.courseStar = '$star'";
		// Gets the data from the database
		$result = mysql_query($sql_query);
		$fields_cnt = mysql_num_fields($result);

		$schema_insert = '';

		for ($i = 0; $i < $fields_cnt +1; $i++)
		{
			$l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
			stripslashes(mysql_field_name($result, $i))) . $csv_enclosed;
			$schema_insert .= $l;
			$schema_insert .= $csv_separator;
		} // end for

		$out = trim(substr($schema_insert, 0, -1));
		$out .= $csv_terminated;

		// Format the data
		while ($row = mysql_fetch_array($result))
		{
			$schema_insert = '';
			for ($j = 0; $j < $fields_cnt; $j++)
			{
				if ($row[$j] == '0' || $row[$j] != '')
				{
					if($row[$j]=="Y")
					{
						$schema_insert .= "Left Early";
						break;
					}
					if($row[$j]=="N")
					{
						$schema_insert .= "";
						break;
					}
					if ($csv_enclosed == '')
					{
						$schema_insert .= $row[$j];
					} 
					else
					{
						$schema_insert .= $csv_enclosed . 
						str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$j]) . $csv_enclosed;
					}
				} 
				else
				{
					$schema_insert .= '';
				}
				if ($j < $fields_cnt - 1)
				{
					$schema_insert .= $csv_separator;
				}
			} // end for
			$out .= $schema_insert;
			$out .= $csv_terminated;
		} // end while

		$sql_query1 = "SELECT studentEcom,courseStar,note,Date from notes where coursestar= '$star'";

		// Gets the data from the database
		$result1 = mysql_query($sql_query1);
		$fields_cnt1 = mysql_num_fields($result1);

		$schema_insert1 = '';

		for ($i = 0; $i < $fields_cnt1; $i++)
		{
			$l = $csv_enclosed . str_replace($csv_enclosed, $csv_escaped . $csv_enclosed,
			stripslashes(mysql_field_name($result1, $i))) . $csv_enclosed;
			$schema_insert1 .= $l;
			$schema_insert1 .= $csv_separator;
		} // end for

		$out1 = trim(substr($schema_insert1, 0, -1));
		$out1 .= $csv_terminated;

		// Format the data
		while ($row = mysql_fetch_array($result1))
		{
			$schema_insert1 = '';
			for ($j = 0; $j < $fields_cnt1; $j++)
			{
				if ($row[$j] == '0' || $row[$j] != '')
				{
					if ($csv_enclosed == '')
					{
						$schema_insert1 .= $row[$j];
					} 
					else
					{
						$schema_insert1 .= $csv_enclosed . 
					str_replace($csv_enclosed, $csv_escaped . $csv_enclosed, $row[$j]) . $csv_enclosed;
					}
				} 
				else
				{
					$schema_insert1 .= '';
				}
				if ($j < $fields_cnt1 - 1)
				{
					$schema_insert1 .= $csv_separator;
				}
			} // end for
			$out1 .= $schema_insert1;
			$out1 .= $csv_terminated;
		} // end while
		
		$out2 = $out."\r\n".$out1;
		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Length: " . strlen($out2));
		header("Content-type: text/x-csv");
		header("Content-Disposition: attachment; filename=$filename");
		echo $out2;
		exit;
	}//exportAttendance() ends
	
	
	/* ***************************** Attendance History ************************************/
	//attendanceHistory() starts
	function attendanceHistory()
	{
		$connection = dbConnect();
		mysql_select_db("AttendanceTracking");

		$uid = $_SESSION['ecom'];
		$role1=" ";
		$getRole = mysql_query("SELECT star FROM courseinfo WHERE GA = '$uid'", $connection) or die (mysql_error());
		if(mysql_num_rows($getRole) > 0) 
		{
			if($_SESSION['role'] != "instructor")
			{
				list($star) = mysql_fetch_array($getRole);
				$role1 = "GA";
				header('location: https://www.wiu.edu/citr/AttendanceResponsive/view/not_authorised.php?starnumber=' . $star);exit;
			}
		}
		
		//commented on 03 June 2019
/*
		
		$historyQuery = mysql_query("SELECT DISTINCT courseStar , title, section, cnumber , term, ecom FROM	attendance , courseinfo	WHERE courseStar = star  and ecom = '$uid' GROUP BY	courseStar
									ORDER BY term DESC ",$connection) or die (mysql_error());
		
*/							
									
			$historyQuery = mysql_query("SELECT DISTINCT star , title, section, cnumber , term, ecom FROM	courseinfo	WHERE star In (select star from courseinfo where ecom = '$uid') GROUP BY	star
									ORDER BY term DESC ",$connection) or die (mysql_error());
									
									
									
									

		$total_rows_obtained=mysql_num_rows($historyQuery);

		
		$list = "";
		$i = 0;
		while (list($star , $c_title, $c_section, $c_number, $term) = mysql_fetch_array($historyQuery)) 
		{	
			$attendanceHistory[$i] = array();
			$newterm = term($term);
			$termYear = substr($term, 0, 4);
			$url = "?action=viewClassSummary&termYear=$termYear&subjectId=$star";
			array_push($attendanceHistory[$i], $star, $c_title, $c_number, $c_section, $newterm, $url);
			$i++;
		}
		require_once('view/view_attendance_history.php');
	}//attendanceHistory() starts
	
	/**** function printPreview() starts ****/
	function printPreview($star_number, $data_value = "") 
	{
		$uid = $_SESSION['ecom'];   /*Added by Harika Annabathina*/
		$connection = dbConnect();
		mysql_select_db("AttendanceTracking");
		$query = mysql_query("SELECT ecom,GA FROM courseinfo 
											WHERE star='$star_number'
											AND (ecom='$uid' OR GA = '$uid') /* Added by Harika Annabathina*/
											",$connection) or die("Error: ".mysql_error());
						
		$num_rows = mysql_num_rows($query); 
		if($num_rows > 0)
		{
			list($userecom,$GAecom) = mysql_fetch_array($query);
		}
		else
		{
			$content="There is no course exists with ";
			$content .=$star_number;
			require_once('view/no_record.php');exit;
		}
		
		$courseInfo = ldap_course($star_number);
		$rosterInfo = getLDAPstudentList($star_number);
		$title = $courseInfo[0]["cn"][0] . " - " . $courseInfo[0]["wiucoursesection"][0];
		
		if($rosterInfo["count"] > 0) 
		{
			$stud_rec_array = array();
			for($i = 0; $i < $rosterInfo["count"]; $i++) 
			{
				$fn = $rosterInfo[$i]["givenname"][0];
				$ln = $rosterInfo[$i]["sn"][0];
				$student = array($ln.", ".$fn);
				array_push($stud_rec_array, $student);
			}
			sort($stud_rec_array);
			require_once('view/print_page.php');
		}
		else
		{
			require_once('view/no_record.php');
		}
	} //printPreview() ends
	
	// This function help to print multiple sign in sheet based on date selected
	// function printMultiplePreview() starts
	function printMultiplePreview($star_number) 
	{
		$uid = $_SESSION['ecom']; /*Added by Harika Annabathina*/
		$connection = dbConnect();
		mysql_select_db("AttendanceTracking");
		$query = mysql_query("SELECT ecom,GA FROM courseinfo 
											WHERE star='$star_number'
											AND (ecom='$uid' OR GA = '$uid') /* Added by Harika Annabathina*/
											",$connection) or die("Error: ".mysql_error());
						
		$num_rows = mysql_num_rows($query); 
		if($num_rows > 0)
		{
			list($userecom,$GAecom) = mysql_fetch_array($query);
			$courseInfo = ldap_course($star_number);
			$rosterInfo = getLDAPstudentList($star_number);
			$title = $courseInfo[0]["cn"][0] . " - " . $courseInfo[0]["wiucoursesection"][0];
			if($rosterInfo["count"] > 0) 
			{
				$stud_rec_array = array();
				for($i = 0; $i < $rosterInfo["count"]; $i++) 
				{
					$fn = $rosterInfo[$i]["givenname"][0];
					$ln = $rosterInfo[$i]["sn"][0];
					$student = array($ln.", ".$fn);
					array_push($stud_rec_array, $student);
				}
				require_once('view/multiple_print_page.php');
			}
		}
		else
		{
			$content="There is no course exists with ";
			$content .=$star_number;
			require_once('view/no_record.php');
		}
	}// function printMultiplePreview() ends
	
	/**** function setPreference() starts ****/
	function setPreference($star, $pref, $pref2, $requiredmail)
	{
		$connection = dbConnect();
		mysql_select_db("AttendanceTracking");
		$minimumabsents=$pref2;
		$Cshare = $_POST['chairshare'];
		$Ashare = $_POST['advisorshare'];
		$uid = $_SESSION['ecom'];
		$mailtostudent=0;
		$mailtoinstructor=0;
		$mailtoadvisor=0;
		$warningmail=0;
		$afterlimit=0;
		$eachabsent=0;
		$emailmechanges = 0;
		if(in_array("stumail", $requiredmail))
		{
			$mailtostudent=1;
		}
		if(in_array("instmail", $requiredmail))
		{
			$mailtoinstructor=1;
		}
		if(in_array("advmail", $requiredmail))
		{
			$mailtoadvisor=1;
		}
		if(in_array("beforeabsenselimit", $requiredmail))
		{
			$warningmail=1;
		}
		if(in_array("afterabsenselimit", $requiredmail))
		{
			$afterlimit=1;
		}
		if(in_array("emaileachabsent", $requiredmail))
		{
			$eachabsent=1;
		}
		if(in_array("emailchanges0", $requiredmail))
		{
			$msgupdate = mysql_query("select subscribed from usersubscriptions where ecom='$uid'");
			if(mysql_num_rows($querying) >= 1)
			{
				list($subscribed) = mysql_fetch_array($msgupdate);
				if($subscribed == 0)
				{
					$emailmechanges = 0;
				}
				else
				{
					$emailmechanges =1;
				}
			}
		}
		if(in_array("emailchanges1", $requiredmail))
		{
			$msgupdate = mysql_query("select subscribed from usersubscriptions where ecom='$uid'");
			if(mysql_num_rows($querying) >= 1)
			{
				list($subscribed) = mysql_fetch_array($msgupdate);
				if($subscribed == 1)
				{
					$emailmechanges =1;
				}
				else
				{
					$emailmechanges =0;
				}
			}
		}
		$querying = mysql_query("select subscribed from usersubscriptions where ecom = '$uid'");
		if(mysql_num_rows($querying) >= 1)
		{
			 $update1 = ("UPDATE usersubscriptions SET subscribed = '$emailmechanges' where ecom ='$uid'");
		}
		else
		{
			mysql_query("INSERT INTO usersubscriptions values ('$uid','$emailmechanges')");
		}

		$query1= mysql_query("SELECT id FROM courseinfo where ecom = '$uid' and star='$star'");
		if(mysql_num_rows($query1) === 1){
			list($cid) = mysql_fetch_array($query1);
		}
		if($Cshare == true)
		{
			$cshr = 1;
		}
		else
		{
			$cshr = -1;
		}

		if($Ashare == true)
		{
			$ashr = 1;
		}
		else
		{
			$ashr = -1;
		}
		$querying1 = mysql_query("select chairshare,advisorshare from shareAttendance where ecom = '$uid' and courseid='$cid'");

		if(mysql_num_rows($querying1) >= 1)
		{
			$update2 = ("UPDATE shareAttendance SET chairshare = '$cshr',advisorshare = '$ashr' where ecom ='$uid' and courseid='$cid'");
		}
		else
		{
			$insert1 = ("INSERT INTO shareAttendance values ('$uid','$cshr','$ashr','$cid')");
		}

		$getAttendance = mysql_query("SELECT courseinfo FROM userpreference WHERE courseinfo='$star' and  ecom = '$uid'",$connection) or die(mysql_error());

		$getPresentCountquery1 = mysql_query(" SELECT COUNT(*) FROM userpreference WHERE courseinfo='$star' ",$connection);
		$getPreferenceCount1 =mysql_result($getPresentCountquery1,0);	
							
		$insert = "INSERT INTO
							userpreference
						VALUES (
							NULL,
							'$star',
							'$pref',
							'$uid', 
							'$minimumabsents',
							'$mailtostudent',
							'$mailtoinstructor',
							'$mailtoadvisor',
							'$warningmail',
							'$afterlimit',
							'$eachabsent'
							)";
					
		$update = "UPDATE
							userpreference
						SET
							preference =  '$pref'
						,
							absenceLimit = '$minimumabsents' 
						,
							studentmail = '$mailtostudent'
						,
							instructormail = '$mailtoinstructor'
						,	
							advisormail = '$mailtoadvisor'
						,   
							beforeabslimit = '$warningmail'
						,
							afterabslimit = '$afterlimit'
						,
							eachabsent = '$eachabsent'
						WHERE
							courseinfo = '$star' and ecom = '$uid'";
		
		$rows = mysql_num_rows($getAttendance);

		if($rows > 0) 
		{
			//echo dsc;
			$result = mysql_query($update,$connection);
			$result1 = mysql_query($update1,$connection);

			if(!(empty($update2)))
			{
				$result2 = mysql_query($update2,$connection);
			}
			else
			{
				$result2 = mysql_query($insert1,$connection);
			}
			$msg = "The user preference was successfully updated.";
		}
		else
		{
			$result = mysql_query($insert,$connection);
			$msg = "The user preference was successfully updated.";
		}
		mysql_close($connection);
		require_once("view/preference_added.php");
	}//function setPreference() ends
	
	//**************************** function attendancePreference() starts **************************************
	function attendancePreference($star)
	{
		$attendancePref = array();
		$uid=$_SESSION['ecom'];
		$connection=dbConnect();
		mysql_select_db("AttendanceTracking");
		$query = mysql_query("SELECT ecom,GA FROM courseinfo 
								WHERE star='$star'
								AND (ecom='$uid' OR GA = '$uid') /* Added by Harika Annabathina*/
								",$connection) or die("Error: ".mysql_error());
						
		$num_rows = mysql_num_rows($query); 
		
		if($num_rows > 0)
		{
			list($userecom,$GAecom) = mysql_fetch_array($query);
		}
		else
		{
			$content="There is no course exists with ";
			$content .=$star;
			require_once("view/no_record.php");
			exit;
		}
		
		$getAttendance = mysql_query("SELECT 
											preference	
									FROM 
										userpreference
									WHERE 
										courseinfo='$star' and  ecom = '$uid'
									",$connection) or die(mysql_error());
									
		list($preference) = mysql_fetch_array($getAttendance);
		$query1 = mysql_query("SELECT  preference from userpreference where courseinfo='$star'",$connection)    or die(mysql_error());
		$preferenceold = mysql_result($query1,0); 
		
		$info_query = "SELECT absenceLimit,studentmail,instructormail,advisormail,beforeabslimit,afterabslimit,eachabsent FROM userpreference WHERE courseinfo ='$star'";
		$info_result = mysql_query($info_query);
		$info_row = mysql_fetch_array($info_result);
		$absenceLimitold = $info_row['absenceLimit'];
		$studentmail12 = $info_row['studentmail'];
		$instructormail12 = $info_row['instructormail'];
		$advisormail12 = $info_row['advisormail'];
		$beforeabsenselimit = $info_row['beforeabslimit'];
		$afterabsenselimit = $info_row['afterabslimit'];
		$eachabsentValue = $info_row['eachabsent'];
		
		$querying = mysql_query("select subscribed from usersubscriptions where ecom='$uid'");
		if(mysql_num_rows($querying) >= 1)
		{
			list($emailmechangesquery) = mysql_fetch_array($querying);
		}
		
		$courseid= mysql_query("SELECT id FROM courseinfo where ecom = '$uid' and star='$star'");
        if(mysql_num_rows($courseid) >= 1)
		{
			list($cid) = mysql_fetch_array($courseid);
		}
		
        $chairsharequery = mysql_query("select chairshare from shareAttendance where ecom='$uid' and courseid='$cid'");
		if(mysql_num_rows($chairsharequery) >= 1)
		{
			list($chairshare) = mysql_fetch_array($chairsharequery);
		}
        
		$advisorsharequery = mysql_query("select advisorshare from shareAttendance where ecom='$uid' and courseid='$cid'");
		if(mysql_num_rows($advisorsharequery ) >= 1)
		{
			list($advisorshare) = mysql_fetch_array($advisorsharequery );
		}
        //array_push($attendancePref, $preferenceold, $absenceLimitold, $studentmail12, $instructormail12, $advisormail12, $beforeabsenselimit, $afterabsenselimit, $eachabsentValue, $emailmechangesquery, $chairshare, $advisorshare);
		require("view/set_user_preference.php");
		mysql_close($connection);
	}// function attendancePreference() ends
	
	/**** function statistics() starts ****/
	function statistics( $termYear, $star ) 
	{
		$uid=$_SESSION['ecom'];
		$instructorInfo = userldap($uid);
		$insStars = array();
		for($x = 0; $x < $instructorInfo[0]["wiustarinstructor"]["count"]; $x++) {
			$starNo =  $instructorInfo[0]["wiustarinstructor"][$x];
			array_push($insStars,$starNo);
		}
		
		for($i=0;$i<count($insStars);$i++){
			if($star<=$insStars[$i])
				$validInstructor = true;
		}
		
		/*Determine if the instructor is valid by looking in the courseinfo table instead of checking LDAP. Added by Roger on Jan 8 2015 */
		$connection=dbConnect();
		mysql_select_db("AttendanceTracking");
				$query = mysql_query("SELECT ecom,GA FROM courseinfo 
													WHERE star='$star' and (ecom='$uid' or GA='$uid')
													",$connection) or die("Error: ".mysql_error());
								
				$validinstructurenum_rows = mysql_num_rows($query); 
		if($validinstructurenum_rows > 0)
		{
			$validInstructor = true;
		}
		else
		{
			$validInstructor = false;
		}

		/*End additional code for determining the instructor validity */
		
		if($validInstructor)
		{
			$connection=dbConnect();
			mysql_select_db("AttendanceTracking");
			$query = mysql_query("SELECT ecom,GA,term FROM courseinfo 
												WHERE star='$star' and (ecom='$uid' or GA='$uid')
												",$connection) or die("Error: ".mysql_error());
							
			$num_rows = mysql_num_rows($query); 
			if($num_rows > 0)
			{
				list($userecom,$GAecom,$term) = mysql_fetch_array($query);
			}
			else
			{
				$content = "There is no course exists with ";
				require_once('view/no_record.php');exit;
			}
								
			$getTotalNumber = mysql_query("SELECT 
												distinct attendedDate
										FROM 
											attendance 
										WHERE 
											courseStar='$star' 
										",$connection) or die(mysql_error());
			$totalNumber = mysql_num_rows($getTotalNumber);	
			
			$getAttendance = mysql_query("SELECT 
												studentEcom,attendance,attendedDate,COUNT(studentEcom) as number
										FROM 
											attendance 
										WHERE 
											courseStar='$star' AND `attendedDate` LIKE '%$termYear%'
										GROUP BY 
											studentEcom 
										",$connection) or die(mysql_error());
			$rows = mysql_num_rows($getAttendance);
			//echo $rows;
				if($rows==0)
				{

				
					echo "<script>
alert('No Record Found, You are redirecting to Previous Page');
window.location.href='https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=attendanceHistory';
</script>";
					
				}
			if($rows > 0) 
			{
				
				$totalPresentcount = 0;
				$totalAbsent = 0;
				$totalExcused = 0;
				$totalTardy =0;	
				$totalLeft=0;	
				$studentArray = array();
				for($i=0;$i<$rows;$i++) 
				{
					$result = mysql_fetch_array($getAttendance);
					$r_ecom = $result[0];
					$info = userldap($r_ecom);
					$fname = mysql_real_escape_string($info[0]["givenname"][0]);	
					$lname = mysql_real_escape_string($info[0]["sn"][0]);
					$cn = mysql_real_escape_string($info[0]["cn"][0]);
					$uidnumber = $info[0]["uidnumber"][0];
					$rank = $info[0]["wiuClassification"][0];
					$getPresentCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='present' AND studentEcom='$r_ecom' AND courseStar='$star' 
											",$connection)) or die(mysql_error());
					$getAbsentCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='absent' AND studentEcom='$r_ecom' AND courseStar='$star'
											",$connection)) or die(mysql_error());
					$getExcusedCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='excused' AND studentEcom='$r_ecom' AND courseStar='$star'
											",$connection)) or die(mysql_error());
					$getTardyCount = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM attendance WHERE attendance='tardy' AND studentEcom='$r_ecom' AND courseStar='$star'
											",$connection)) or die(mysql_error());
					$getLeftEarlyCount = mysql_fetch_array(mysql_query("SELECT COUNT(*) FROM LeftEarly INNER JOIN attendance ON LeftEarly.id=attendance.id WHERE LeftEarlyflag='Y' AND studentEcom='$r_ecom' AND courseStar='$star'
											",$connection)) or die(mysql_error());
					
					//Need to get the student name from the attendance table
					$getNames = mysql_fetch_array(mysql_query(" SELECT studentName FROM attendance WHERE studentEcom='$r_ecom' AND courseStar='$star'
											",$connection)) or die(mysql_error());

					
					$totalPresentcount += $getPresentCount[0];
					$totalAbsent += $getAbsentCount[0]; 
					$totalExcused += $getExcusedCount[0];
					$totalTardy += $getTardyCount[0]; 
					$totalLeft+= $getLeftEarlyCount[0];
					
					
					$newtotal = $getPresentCount[0] + $getAbsentCount[0] + $getExcusedCount[0] + $getTardyCount[0];
									  
					$query_attendance = mysql_fetch_array(mysql_query(" select count(*) from  (SELECT distinct count(id), attendedDate
					FROM attendance  WHERE courseStar='$star' Group by attendedDate) as temp",$connection)) or die(mysql_error());
			
					$totalpresent = $query_attendance[0];
					$attendancePercentage = round(($getPresentCount[0]/$query_attendance[0])*100);
					$pa=stripslashes($lname.", ".$fname);
					$student = array($pa, $uidnumber, $getPresentCount[0], $getAbsentCount[0], $getExcusedCount[0], $getTardyCount[0], $getLeftEarlyCount[0], $newtotal, $attendancePercentage, $uidnumber, $star, $r_ecom);
					array_push($studentArray,$student);				
				}
				//echo $totalAbsent;exit;
				sort($studentArray);
				//print_r($studentArray);
				require_once("view/statistics.php");
			}
		}
		else
		{
			$content = "Please try with the course number which is associated to you.";
			require_once('view/no_record.php');exit;
		}
	}//function statistics() ends
	
	//function getPicture() starts
	function getPicture($WIUID , $mail)
	{
		include ('/home/mifdo/.php_oracle_conection');
		
		// Connect to database
		$ConnectionID = oci_connect( $Username, $Password, $ConnectionString );
		if ( ! $ConnectionID ) {
			$ErrorArray = oci_error();
			$ErrorString = htmlentities( $Error[ 'message' ], ENT_QUOTES );
			trigger_error( $ErrorString, E_USER_ERROR );
		}
		
		$pic=$mail.".jpeg"; 
		
		$open1 = fopen("/www/mnt/https/mifdo/AttendanceTracking/picture/".$pic, "r"); //open the file .
		if($open1)
		{
			return $pic;
		}
		else
		{
			$SQLText = "SELECT DBD_IMAGE FROM LOCAL_PATRONPICTURES" ;
			$SQLText .= " where primarykey='$WIUID'" ;
			$StatementID = oci_parse( $ConnectionID, $SQLText );
			if ( ! $StatementID ) {
				$ErrorArray = oci_error();
				$ErrorString = htmlentities( $Error[ 'message' ], ENT_QUOTES );
				trigger_error( $ErrorString, E_USER_ERROR );
			}
			
			// Perform the logic of the query
			$Result = oci_execute( $StatementID );
			if ( ! $Result ) {
				$ErrorArray = oci_error();
				$ErrorString = htmlentities( $Error[ 'message' ], ENT_QUOTES );
				trigger_error( $ErrorString, E_USER_ERROR );
			}
			
			// display query result
			while ($DataArray= oci_fetch_assoc($StatementID)) 
			{
			if($DataArray['DBD_IMAGE']!="")
				$Data=$DataArray['DBD_IMAGE']->load();

			} 
			
			header( "Content-type: image/JPEG" );
			
			$size = 150;  // new image width
			$src = imagecreatefromstring($Data);
			$width = imagesx($src);
			$height = imagesy($src);
			$aspect_ratio = $height/$width;
				
			if ($width <= $size) 
			{
				  $new_w = $width;
				  $new_h = $height;
			} else 
			{
				  $new_w = $size;
				  $new_h = abs($new_w * $aspect_ratio);
			}
			$img = imagecreatetruecolor($new_w,$new_h);
			imagecopyresized($img,$src,0,0,0,0,$new_w,$new_h,$width,$height);
			ob_start(); // start a new output buffer
			imagejpeg($img);
			$i = ob_get_clean(); 
			imagedestroy($img); 	
			ob_clean();
			$file = $mail.".jpeg"; 
			$open = fopen("/www/mnt/https/mifdo/AttendanceTracking/picture/".$file, "w+"); //open the file .
			fwrite($open, $i); //print 
			fclose($open); // you must ALWAYS close the opened file once you have finished.
			oci_free_statement( $StatementID );
			oci_close( $ConnectionID );
			header("Content-Type: text/html");
			return($file);
		}
		//function getPicture() ends
	}
	
	//function getAdvisorDetails() starts
	function getAdvisorDetails($star, $ecom)
	{
		$studentInfo = ldap_wiuID($ecom);
		$studentName = $studentInfo[0]["cn"][0];
		$advisorid = $studentInfo[0]["wiuadvisorid"][0];
		$advDetails = advisorwiuID($advisorid);
		$advName = $advDetails[0]['cn'][0];
		$advContactNumber = $advDetails[0]['telephonenumber'][0];
		$advEmail = $advDetails[0]["mail"][0];
		require_once("view/advisor_details.php");
	}//function getAdvisorDetails() ends
	
	//function getAdvisorDetails() starts
	function advisorwiuID($wiuid) 
	{
		require("/home/mifdo/https-files/php_ldap_bind.inc");
        
        $ds=ldap_connect("ldap.wiu.edu:389");  // must be a valid LDAP server!
        if (!$ds)
			return "Unable to connect to the LDAP server.  Contact the UCSS Helpdesk at 298-2704 for support.";
        
		$r=ldap_bind($ds,$BindDN,$BindPW);
        if ($r==0)
			return "Unable to bind to the LDAP server with the specified username and password.  Contact the UCSS Helpdesk at 298-2704 for support.";
			
		$criteria = "wiuid=".$wiuid;		//"wiuregisteredstar=".$star;
				
        $sr = ldap_search($ds, "ou=people,dc=wiu,dc=edu", $criteria);
        if($sr == 0)
            return array("Unable to find any students registered for the course.  Please try again or call the UCSS Helpdesk at 298-2704 if you continue to have trouble.","Error");
		$userInfo = ldap_get_entries($ds, $sr);
		return $userInfo;
	}//function getAdvisorDetails() ends

	//************************ LDAP function to get student information ******************************
	function studentldap($id) {
		require("/home/mifdo/https-files/php_ldap_bind.inc");
        
        $ds=ldap_connect("ldap.wiu.edu:389");  // must be a valid LDAP server!
        if (!$ds)
			return "Unable to connect to the LDAP server.  Contact the UCSS Helpdesk at 298-2704 for support.";
        
		$r=ldap_bind($ds,$BindDN,$BindPW);
        if ($r==0)
			return "Unable to bind to the LDAP server with the specified username and password.  Contact the UCSS Helpdesk at 298-2704 for support.";
			
		$criteria = "uidnumber=".$id;
			
        // Search uid entry
        $sr = ldap_search($ds, "ou=people,dc=wiu,dc=edu", $criteria);
        if($sr == 0)
            return array("Unable to find any students registered with that ID.  Please try again or call the UCSS Helpdesk at 298-2704 if you continue to have trouble.","Error");

		$numEntries = ldap_count_entries($ds, $sr);
		$studentInfo = ldap_get_entries($ds, $sr);
		ldap_close($ds);
		
		if($numEntries > 0)
			return $studentInfo;
	}//function studentldap() ends
	
	//function studentInfo() starts
	function studentInfo($subjectId, $id) 
	{
		$connection = dbConnect();
		mysql_select_db("AttendanceTracking");
        $studentInfo = userldap($id);
		$major2="";
		
		if($studentInfo["count"] > 0) 
		{
		    $name = $studentInfo[0]["cn"][0];
			$firstName = $studentInfo[0]["givenname"][0];
	        $lastName = $studentInfo[0]["sn"][0];
	        $mail = $studentInfo[0]["mail"][0];
	        $uid = $studentInfo[0]["uid"][0];
	     	$wiuID =  $studentInfo[0]["wiuid"][0];
            $title = $studentInfo[0]["title"][0];
			if($studentInfo[0]["wiumajor"]["count"]>1)
           	{	
				$major2= $studentInfo[0]["wiumajor"][1];
			}
			$major = $studentInfo[0]["wiumajor"][0];
			$minor = $studentInfo[0]["wiuminor"][0];
			$dept = $studentInfo[0]["department"][0];
			$rank = $studentInfo[0]["wiuclassification"][0];
            $advisor = $studentInfo[0]["wiuadvisorid"][0];
			$classes = $studentInfo[0]["wiuregisteredstar"];
			
			$advisorInfo = advisorwiuID($advisor);
	
			if($advisorInfo["count"] > 0)
			{
				for($i=0; $i<$advisorInfo["count"]; $i++) 
				{
					$advisorName = $advisorInfo[$i]["cn"][0];
					$advisormail = $advisorInfo[$i]["mail"][0];
				}
			}

			$info = getPicture($wiuID , $mail);

			if($studentInfo[0]["wiumajor"]["count"]>1)
			{
				$values = $studentInfo[0]["wiumajor"][1];
			}
			
			$qry1 = "SELECT COUNT(attendance) FROM attendance WHERE ";
			$qry2 = " AND studentEcom='$uid' AND courseStar='$subjectId'";
			
			$getCount = mysql_fetch_array(mysql_query(" SELECT (".$qry1." attendance='present' ".$qry2."),
							 					(".$qry1."  attendance='absent' ".$qry2."),
												(".$qry1."  attendance='excused' ".$qry2."),
												(".$qry1."  attendance='tardy' ".$qry2.")
									",$connection)) or die(mysql_error());
			list($getLeftEarlyCount) = mysql_fetch_array(mysql_query(" SELECT COUNT(*) FROM LeftEarly INNER JOIN attendance ON LeftEarly.id=attendance.id WHERE LeftEarlyflag='Y' AND studentEcom='$uid' AND courseStar='$subjectId'",$connection)) 
			or die(mysql_error());
			
			
			$getDetails = mysql_query("SELECT 
											DATE_FORMAT(attendedDate,'%M %d, %Y %a'), attendance, LeftEarlyflag 
										FROM 
											attendance LEFT JOIN LeftEarly
										ON 
											attendance.id=LeftEarly.id
										WHERE 
											studentEcom='$uid' 
										AND 
											courseStar='$subjectId' 
										ORDER BY 
											attendedDate 
										",$connection) or die(mysql_error());
			$x = 0;							
			if(mysql_num_rows($getDetails)>0) {
				while(list($dateDetail,$desc,$left) = mysql_fetch_array($getDetails)) 
				{
					$attend = strtotime($dateDetail);
					$date = date('l - F  d, Y ', $attend) ;
					
					if($left=="Y")
					{
						$desc=$desc." Left Early";
					}
					$row[$x] = array();
					array_push($row[$x], $date, ucfirst($desc));
					$x++;
				}
			}
			require_once("view/student_info.php");
		}
	}//function studentInfo() starts
?>


