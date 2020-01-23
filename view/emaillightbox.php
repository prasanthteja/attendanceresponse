<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Email Student Report</title>

   <script language="javascript" type="text/javascript">
 

</script>	
</head>

 <body>

     
<?php  
$name=$_GET['student'];
$stuEcom=$_GET['stuEcom'];
$star=$_GET['star'];
$cdate=$_GET['classdate'];
//onsubmit="top.close();"
?>
<p>Detail Messaging for:<?php echo " ".$name; ?>   
<!--<FORM  action="/citr/AttendanceTracking/index.sphp#Results" METHOD=POST  >-->

<form name='form1' method='post' action="/citr/AttendanceTracking/?action=selectCourse&subjectId=<?php echo $star;?>">


<TEXTAREA NAME="text_area" COLS=50 ROWS=10 style="background-color:#E2E2E2;"></TEXTAREA>
<p>
<input type="hidden" name="stuEcom" value=<?php echo $stuEcom;?> id="stuEcom">
<input type="hidden" name="stuname" value=<?php echo $name;?> id="stuname">
<input type="hidden" name="star" value=<?php echo $star;?> id="star">
<input type="hidden" name="cdate" value=<?php echo $cdate;?> id="cdate">


<input type="submit" name="Submit" id="Submit" value="Email Student*" class="submitButton"  />
<input type="submit" name="Submit" id="Submit" value="Email Student & Advisor*" class="submitButton"/>
</FORM>
</p>
<p>

*Email will include a cc: to the instructor and a full attendance summary for this class.</p>

<?php/* if($_REQUEST['Submit']=='Save for Teach')
{
  echo "Save ";
}
else
if(!empty($_REQUEST['Save and Email']))
{
  //Do insert Here
  
}
*/?>

</body>
</html>


