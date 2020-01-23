<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Attendance Tracking</title>

    <!-- Bootstrap Core CSS -->
    <link href="https://www.wiu.edu/citr/AttendanceResponsive/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom CSS -->
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">
	<link href="https://cdn.datatables.net/fixedheader/3.1.3/css/fixedHeader.dataTables.min.css" rel="stylesheet">
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.1/css/font-awesome.min.css">
	<link rel="stylesheet" href="https://www.wiu.edu/citr/AttendanceResponsive/css/print.css">
	<link href="https://www.wiu.edu/citr/AttendanceResponsive/css/attendanceresponsive.css" rel="stylesheet">

	
	<!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
        <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
        <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
	
	<!-- jQuery -->
    <script src="https://www.wiu.edu/citr/AttendanceResponsive/js/jquery-1.11.1.js"></script>

    <!-- Bootstrap Core JavaScript -->
    <script src="https://www.wiu.edu/citr/AttendanceResponsive/js/bootstrap.min.js"></script>

</head>

<body>

    <!-- Navigation -->
    <nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
            </div>
            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                <ul class="nav navbar-nav">
                    <li>
                        <a href="https://www.wiu.edu/citr/AttendanceResponsive/index.sphp">Course List</a>
                    </li>
                    <li>
                        <a href= "https://www.wiu.edu/citr/AttendanceResponsive/index.sphp?action=attendanceHistory">Attendance History</a>
                    </li>
                    
					<li class = "dropdown" >
						<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">Others <span class="caret"></span>
						</a>
						<ul class="dropdown-menu">
							<li> <a href="https://www.wiu.edu/citr/ORCCs/" target = "_blank">Online Reporting for Cancellation of classes</a></li>
							<li> <a href="https://www.wiu.edu/citr/resources/" target = "_blank">Return to Resources</a> </li>
							<li> <a href="https://www.wiu.edu/citr/AttendanceTracking/functions/dashboard.function.sphp" target = "_blank">Dashboard</a> </li>
						</ul>
						
					</li>
					<li>
                        <a href="https://auth.wiu.edu/cas/logout">Logout</a>
                    </li>
                </ul>
            </div>
			<a href= "#" class="back-to-top">Back to Top</a>
            <!-- /.navbar-collapse -->
        </div>
        <!-- /.container -->
    </nav>