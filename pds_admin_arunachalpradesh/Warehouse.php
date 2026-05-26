<?php
require('util/Connection.php');
require('util/SessionCheck.php');
if(empty($_SESSION['csrf_token'])){
$_SESSION['csrf_token']=bin2hex(random_bytes(32));
}
require('Header.php');
?>
<style>
     td {
            font-size: 16px; /* Increase font size for table headers and data cells */
        }
        .table thead tr th {
    background-color: #95b75d !important;
    /* border: 2px solid #777; */
    color: black;
    /* Optional: Font size for table header */
}
    </style>
	<script src="crypto-js/crypto-js.js"></script>
    <script src="js/Encryption.js"></script>

                <!-- START BREADCRUMB -->
                <ul class="breadcrumb">
                    <li><a href="#">Home</a></li>
                    <li class="active">Warehouse</li>
                </ul>
                <!-- END BREADCRUMB -->


				<!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">

                    <div class="row">
                        <div class="col-md-12">

                            <!-- START SIMPLE DATATABLE -->
                            <div class="panel panel-default">
							<div class="panel-heading">
                                    <h3 class="panel-title">Warehouse</h3>
                                </div>
								<a href="BulkWarehouseStatusChange.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-info">District-Wise Status Change</button></a>
								<a href="BulkWarehouseDataEdit.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-warning">Bulk Data Edit</button></a>
								<a href="BulkWarehouseData.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-info">Bulk Data Add</button></a>
								<span style="float:right;margin-top:10px;margin-right:13px"><button type="button" onclick="delete_all()"  class="btn btn-danger">Delete All</button></span>
								<a href="WarehouseAdd.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-success">Add New</button></a>
                                <a href="api/BulkWarehouseDownloadEdit.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-info">Download Data</button></a>
                            
                                <div class="panel-body">
                                 <div class="table-responsive">
                                    <table id="export_table" class="table datatable">
                                        <thead>
                                            <tr>
												<th style="font-size:15px">District</th>
												<th style="font-size:15px">Name of Warehouse</th>
												<th style="font-size:15px">Warehouse ID</th> 
												<th style="font-size:15px">Motorable/Non-Motorable</th>
												<th style="font-size:15px">Warehouse Type</th>
												<th style="font-size:15px">Latitude</th>
												<th style="font-size:15px">Longitude</th>
												<th style="font-size:15px">Storage</th>
												<th style="font-size:16px">Status</th>
												<th style="font-size:16px">Change Status</th>
                                                <th style="font-size:15px">Edit</th>
                                                <th style="font-size:15px">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody>
										<?php
										
										$query = "SELECT * FROM warehouse WHERE 1 ORDER BY district";
										$result = mysqli_query($con,$query);
										$numrows = mysqli_num_rows($result);
										while($row = mysqli_fetch_array($result))
										{
											$temp_id = (string)$row['uniqueid'];
											$status = $row['active'];
											if($status==1){
												$status = "<span style='padding:5px' class='btn-success btn-rounded'>Active</span>";
											}
											else{
												$status = "<span style='padding:5px' class='btn-danger btn-rounded'>InActive</span>";
											}
											echo "<tr><td>{$row['district']}</td>".
											"<td>{$row['name']}</td>".
											"<td>{$row['id']}</td>".
											"<td>{$row['type']}</td>".
											"<td>{$row['warehousetype']}</td>".
											"<td>{$row['latitude']}</td>".
											"<td>{$row['longitude']}</td>".
											"<td>{$row['storage']}</td>".
											"<td>$status</td>".
											 "<td> <button class='btn btn-info btn-rounded' onclick=\"change_status('{$temp_id}')\">Change Status</button></td>".
											 "<td> <button class='btn btn-warning btn-rounded' onclick=\"edit_entry('{$temp_id}')\">Edit</button></td>".
											 "<td> <button class='btn btn-danger btn-rounded' onclick=\"delete_entry('{$temp_id}')\">Delete</button></td></tr>";
										}
										
										?>
                                        </tbody>
										<div id="popup" class="popup" style="z-index:999">

<a class="close" onclick="hidePopup()">×</a>

<div class="col-md-6">

<label>Username*</label>

<input type="text"
class="form-control"
id="username"
required />

<input type="hidden"
id="deleteid">

<input type="hidden"
id="csrf_token"
value="<?php echo $_SESSION['csrf_token']; ?>">

</div>

<div class="col-md-6">

<label>Password*</label>

<input type="password"
class="form-control"
id="password"
required>

</div>

<div class="col-md-12">

<label>Captcha*</label>

<input type="text"
class="form-control"
id="captchainput"
placeholder="Enter captcha"
required>

</div>

<div class="col-md-12">

<div id="image"
style="width:150px;
height:50px;
background:#333;
color:white;
font-size:24px;
text-align:center;
padding:5px;
margin-top:10px;">
</div>

<button onclick="generateCaptcha()">
Refresh
</button>

</div>

<center>

<button class="btn btn-primary"
type="button"
onClick="VerifyAndDelete()">

Verify

</button>

</center>

</div>
                                    </table>
                                  </div>
                                </div>
                            </div>
                            <!-- END SIMPLE DATATABLE -->

                        </div>
                    </div>

                </div>
                <!-- PAGE CONTENT WRAPPER -->
            </div>
            <!-- END PAGE CONTENT -->
        </div>
        <!-- END PAGE CONTAINER -->



    <!-- START SCRIPTS -->
        <!-- START PLUGINS -->
        <script type="text/javascript" src="js/plugins/jquery/jquery.min.js"></script>
        <script type="text/javascript" src="js/plugins/jquery/jquery-ui.min.js"></script>
        <script type="text/javascript" src="js/plugins/bootstrap/bootstrap.min.js"></script>
        <!-- END PLUGINS -->

        <!-- THIS PAGE PLUGINS -->
        <script type='text/javascript' src='js/plugins/icheck/icheck.min.js'></script>
        <script type="text/javascript" src="js/plugins/mcustomscrollbar/jquery.mCustomScrollbar.min.js"></script>
        <script type="text/javascript" src="js/plugins/datatables/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/tableExport.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jquery.base64.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/html2canvas.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/sprintf.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jspdf/jspdf.js"></script>
		<script type="text/javascript" src="js/plugins/tableexport/jspdf/libs/base64.js"></script>
		
		
        <script type="text/javascript" src="js/plugins.js"></script>
        <script type="text/javascript" src="js/actions.js"></script>
        <!-- END PAGE PLUGINS -->

        <!-- START TEMPLATE -->
       
        <!-- END TEMPLATE -->

		<script>
		function post(params,file) {

			method = "post";
			path = file;

			var form = document.createElement("form");
			form.setAttribute("method", method);
			form.setAttribute("action", path);

			for(var key in params) {
				if(params.hasOwnProperty(key)) {
					var hiddenField = document.createElement("input");
					hiddenField.setAttribute("type", "hidden");
					hiddenField.setAttribute("name", key);
					hiddenField.setAttribute("value", params[key]);
					form.appendChild(hiddenField);
				 }
			}

			document.body.appendChild(form);
			form.submit();
		}

		document.getElementById('popup').style.display = 'none';

		var captcha;
		
		function generateCaptcha() {
			document.getElementById('captchainput').value = '';
			captcha = document.getElementById('image');
			var uniquechar = '';
			const randomchar = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
			for (let i = 0; i < 6; i++) {
				uniquechar += randomchar.charAt(Math.floor(Math.random() * randomchar.length));
			}
			captcha.innerHTML = uniquechar;
			
			// Store CAPTCHA in session
			$.ajax({
				url: 'captcha.php',
				method: 'POST',
				data: { captcha: uniquechar },
				success: function(response) {
					console.log('CAPTCHA stored');
				}
			});
		}
		
		function verifyCaptcha() {
			const usr_input = document.getElementById('captchainput').value;
			if (usr_input === captcha.innerHTML) {
				return true;
			} else {
				alert('Incorrect Captcha');
				generateCaptcha();
				return false;
			}
		}
		
		function delete_entry(temp_id){
			document.getElementById('popup').style.display = 'block';
			document.getElementById('deleteid').value = temp_id;
		}

		function edit_entry(temp_id){
			post({uid: temp_id} ,"WarehouseEdit.php");
		}
		
		function change_status(temp_id){
			post({uid: temp_id} ,"api/WarehouseStatus.php");
		}
		
		function delete_all(){
			document.getElementById('popup').style.display = 'block';
			document.getElementById('deleteid').value = "all";
		}
		
		function VerifyAndDelete(){

			if(!verifyCaptcha()){
			return;
			}
			

			var username=
			document.getElementById('username').value;

			var password=
			document.getElementById('password').value;

			var captchainput=
			document.getElementById('captchainput').value;

			var csrf=
			document.getElementById('csrf_token').value;

			var temp_id=
			document.getElementById('deleteid').value;

			var nonceValue="nonce_value";

			let encryption=
			new Encryption();

			var encrypted=
			encryption.encrypt(password,nonceValue);

			post({

			uid:temp_id,

			username:username,

			password:encrypted,

			captchainput:captchainput,

			csrf_token:csrf

			},"api/WarehouseDelete.php");

			}
		
		function hidePopup() {
            document.getElementById('popup').style.display = 'none';
        }
		
		</script>
    </body>
</html>
