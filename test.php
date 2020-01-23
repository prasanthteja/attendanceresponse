<html>
<head>
<style>
body {
    font-family: sans-serif;
    position:relative;
    background:#40464b;
    height:100%;
    padding:40px 30%;
    margin:0;
}

input[type="checkbox"] {
    display:none;
}

input[type="checkbox"] + label {
    color:#f2f2f2;
}

input[type="checkbox"] + label span {
    display:inline-block;
    width:19px;
    height:19px;
    margin:-2px 10px 0 0;
    vertical-align:middle;
    background:url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/210284/check_radio_sheet.png) left top no-repeat;
    cursor:pointer;
}

input[type="checkbox"]:checked + label span {
    background:url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/210284/check_radio_sheet.png) -19px top no-repeat;
}

input[type="radio"] {
    display:none;
}

input[type="radio"] + label {
    color:#f2f2f2;
    font-family:Arial, sans-serif;
}

input[type="radio"] + label span {
    display:inline-block;
    width:19px;
    height:19px;
    margin:-2px 10px 0 0;
    vertical-align:middle;
    background:url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/210284/check_radio_sheet.png) -38px top no-repeat;
    cursor:pointer;
}

input[type="radio"]:checked + label span {
    background:url(https://s3-us-west-2.amazonaws.com/s.cdpn.io/210284/check_radio_sheet.png) -57px top no-repeat;
}
</style>
</head>
<body>
	<input type="checkbox" id="c1" name="cc" />
    <label for="c1"><span></span>Check Box 1</label>
    <p>
    <input type="checkbox" id="c2" name="cc" />
    <label for="c2"><span></span>Check Box 2</label>
    <p><br/>
    <input type="radio" id="r1" name="rr" />
    <label for="r1"><span></span>Radio Button 1</label>
    <p>
    <input type="radio" id="r2" name="rr" />
    <label for="r2"><span></span>Radio Button 2</label>
</body>

</html>