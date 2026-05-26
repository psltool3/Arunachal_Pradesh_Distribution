<?php
require('util/Connection.php');
require('util/SessionCheck.php');
// session_start();

if(empty($_SESSION['csrf_token'])){
$_SESSION['csrf_token']=bin2hex(random_bytes(32));
}
require('Header.php');
?>
<style>
    td {
            font-size: 15px; /* Increase font size for table headers and data cells */
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
                    <li class="active">FPS</li>
                </ul>
                <!-- END BREADCRUMB -->


				<!-- PAGE CONTENT WRAPPER -->
                <div class="page-content-wrap">

                    <div class="row">
                        <div class="col-md-12">

                            <!-- START SIMPLE DATATABLE -->
                            <div class="panel panel-default">
							<div class="panel-heading">
                                    <h3 class="panel-title">FPS</h3>
                                </div>
								<a href="BulkFPSStatusChange.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-info">District-Wise Status Change</button></a>
								<a href="BulkFPSDataEdit.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-warning">Bulk Data Edit</button></a>
								<a href="BulkFPSData.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-info">Bulk Data Add</button></a>
								<span style="float:right;margin-top:10px;margin-right:13px"><button type="button" onclick="delete_all()"  class="btn btn-danger">Delete All</button></span>
								<a href="FPSAdd.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-success">Add New</button></a>
                                <a href="api/BulkFPSDownloadEdit.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-info">Download Data</button></a>
                            
								</br></br>
								<div>
								</br></br></br>
                                <a href="api/SmartFPSStatus.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-warning">Model FPS Status Change</button></a>
								<a href="api/NonSmartFPSStatus.php" style="float:right;margin-top:10px;margin-right:13px"><button type="button" class="btn btn-info">Non Smart FPS Status Change</button></a>
								</div>
								
								<div class="row" style="margin-top:60px">
									<div class="col-md-8">
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label class="col-md-3 control-label">Districts</label>
											<div class="col-md-9">  
												<div class="input-group">
												<span class="input-group-addon"><span class="fa fa-certificate"></span></span>						
												<select class="form-control" id="district" name="district" onchange="fetchDataFromServer()">
													<option value=''>Select</option>
													<option value='all'>All</option>
												</select>
												</div>
												<span class="help-block">All option will work only for download</span>
											</div>
										</div>
									</div>
								</div>
								<div class="panel-body">
                                 <div class="table-responsive">
                                     <table id="export_table" class="table">
                                        <thead>
                                            <tr>
												<th style="font-size:16px">District</th>
												<th style="font-size:16px">Name of FPS</th>
												<th style="font-size:16px">FPS ID</th>
												<th style="font-size:16px">Smart FPS/Non Smart FPS</th>
												<th style="font-size:16px">Latitude</th>
												<th style="font-size:16px">Longitude</th>
												<th style="font-size:16px">Demand of FRice</th>
												<th style="font-size:16px">Status</th>
												<th style="font-size:16px">Change Status</th>
                                                <th style="font-size:16px">Edit</th>
                                                <th style="font-size:16px">Delete</th>
                                            </tr>
                                        </thead>
                                        <tbody id="fps_table">
										
                                        </tbody>
										
									<div id="popup" class="popup">
										<a class="close" onclick="hidePopup()" style="font-size:25px">×</a>
										</br></br>
										
										<div class="col-md-6">
										
											<div class="form-group">
                                                <label class="col-md-3 control-label">Username*</label>
                                                <div class="col-md-9">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-info"></span></span>
                                                        <input type="text" class="form-control" id="username" name="username" required />
                                                    </div>
                                                    <span class="help-block">Username</span>
                                                </div>
                                            </div>
											 <input type="hidden" class="form-control" id="deleteid" name="deleteid"  />
											 <input type="hidden" id="csrf_token" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
											
                                        </div>
                                        <div class="col-md-6">
										
										
											<div class="form-group">
                                                <label class="col-md-3 control-label">Password*</label>
                                                <div class="col-md-9">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-info"></span></span>
                                                        <input type="password" class="form-control" id="password" name="password" required />
                                                    </div>
                                                    <span class="help-block">Password</span>
                                                </div>
                                            </div>
											
											
                                        </div>
										
										<div class="col-md-12" style="clear:both;">
											<div class="form-group">
                                                <label class="col-md-3 control-label">Captcha*</label>
                                                <div class="col-md-9">
                                                    <div class="input-group">
                                                        <span class="input-group-addon"><span class="fa fa-shield"></span></span>
                                                        <input type="text" class="form-control" id="captchainput" name="captchainput" placeholder="Enter Captcha" required />
                                                    </div>
                                                    <span class="help-block">Enter the captcha code shown below</span>
                                                </div>
                                            </div>
										</div>
										
										<div class="col-md-12">
											<div class="form-group">
												<div class="col-md-3"></div>
												<div class="col-md-9">
													<div class="row">
														<div class="col-md-7">
															<div id="image" style="box-shadow: 0px 2px 5px rgba(0,0,0,0.3); width: 100%; max-width: 180px; font-weight: 600; height: 60px; color: #fff; user-select: none; text-decoration: line-through; font-style: italic; font-size: x-large; border: #2798d5 2px solid; padding: 10px; background-color: #333; display: inline-block; text-align: center; line-height: 40px;"></div>
														</div>
														<div class="col-md-5">
															<span class="fa fa-refresh" style="font-size: 30px; color: #2798d5; margin-top: 15px; cursor: pointer; display: inline-block;" onclick="generateCaptcha()" title="Refresh Captcha"></span>
														</div>
													</div>
												</div>
											</div>
										</div>
										
										<div class="col-md-12" style="clear:both; margin-top: 20px;">
											<center><button class="btn btn-primary" type="button" onClick="VerifyAndDelete()">Verify</button></center>
										</div>
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
		
		
		<?php  require('DistrictAutocomplete.php'); ?>
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
			document.getElementById('username').value = '';
			document.getElementById('password').value = '';
			generateCaptcha();
		}

		function edit_entry(temp_id){
			post({uid: temp_id} ,"FPSEdit.php");
		}
		
		function change_status(temp_id){
			post({uid: temp_id} ,"api/FPSStatus.php");
		}
		
		function delete_all(){
			document.getElementById('popup').style.display = 'block';
			document.getElementById('deleteid').value = "all";
			document.getElementById('username').value = '';
			document.getElementById('password').value = '';
			generateCaptcha();
		}
		
		function VerifyAndDelete(){

			if (!verifyCaptcha()) {
				return;
			}

			

			var username=document.getElementById('username').value;
			var password=document.getElementById('password').value;
			var captchainput=document.getElementById('captchainput').value;
			var temp_id=document.getElementById('deleteid').value;
			var csrf=document.getElementById('csrf_token').value;

			var nonceValue="nonce_value";

			let encryption=new Encryption();

			var encrypted=encryption.encrypt(password,nonceValue);

			post({

			uid:temp_id,

			username:username,

			password:encrypted,

			captchainput:captchainput,

			csrf_token:csrf

			},"api/FPSDelete.php");

		}
		
		function hidePopup() {
            document.getElementById('popup').style.display = 'none';
        }
		
		
		function fetchDataFromServer(){
			var districtElement = document.getElementById('district');
			var district = districtElement.value;
			
			if(district==""){
				var options = districtElement.options;
				for (var i = 0; i < options.length; i++) {
					if (options[i].value != "all" && options[i].value != "") {
						districtElement.selectedIndex = i;
						district = options[i].value ;
						break;
					}
				}
			}
			
			var dataString = "district=" + district;
			
			$.ajax({
				type: "POST",
				url: "api/fetchFPSData.php",
				data: dataString,
				cache: false,
				error: function(){
					alert("timeout");
					$("#filter_button").attr("disabled",false);
				},
				timeout: 216000,
				success: function(result){
					//console.log(result);
					try{
						$('#fps_table').empty();
						var resultarray = JSON.parse(result);
						var obj = resultarray["data"];
						console.log(obj);
						for (var datafield in obj){
							var temp_id = obj[datafield]["uniqueid"];
							var status = obj[datafield]["active"];
							if(status==1){
								status = "<span style='padding:5px' class='btn-success btn-rounded'>Active</span>";
							}
							else{
								status = "<span style='padding:5px' class='btn-danger btn-rounded'>InActive</span>";
							}
							var subpart = "<tr><td>" +  obj[datafield]["district"] +  "</td><td>"  + obj[datafield]["name"] +  "</td><td>"  + obj[datafield]["id"] +  "</td><td>"  + obj[datafield]["type"] +  "</td><td>"  + obj[datafield]["latitude"] +  "</td><td>"  + obj[datafield]["longitude"] +  "</td><td>"  + obj[datafield]["demand"] +  "</td><td>"  + obj[datafield]["demand_rice"]  + "</td><td>" + status + "</td><td> <button class='btn btn-info btn-rounded' onclick=\"change_status('"+ temp_id + "')\">Change Status</button></td><td> <button class='btn btn-warning btn-rounded' onclick=\"edit_entry('" + temp_id +  "')\">Edit</button></td><td> <button class='btn btn-danger btn-rounded' onclick=\"delete_entry('" + temp_id +"')\">Delete</button></td></tr>";
							$('#fps_table').append(subpart);
						}
					}
					catch (error) {
					}
				}
			});
		}
		
		fetchDataFromServer();
		
			
    </script>
    

		</script>
    </body>
</html>
