<?php
	$db_host = "localhost";
	$db_username = "root";
	$db_pass = "root";
	$db_name = "protocoldb";
	
	mysql_connect("$db_host","$db_username","$db_pass") or die(mysql_error());
	mysql_select_db("$db_name") or die("Database Connection Error");
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Protocol Scrubbing</title>

    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    
    <style>
		.tb{width:100%;}
		.tb .top{background-color:#eee; font-weight:bold;}
		.tb td{padding:5px; border:1px solid #ddd;}
	</style>
</head>

<body style="width:800px; margin:auto;">
	<header class="alert alert-info">
    	<h2>Protocol Scrubbing | <small class="text-muted"><a href="set.php" class="btn btn-primary">Set Permissions</a> <a href="index.php" class="btn btn-success">Scrubbing</a></small></h2>
    </header>
    
    <div class="row">
    	<h2>Perform Scrubbing</h2>
        <hr />
        
        <?php
			function findexts($filename) 
			 { 
				 $filename = strtolower($filename) ; 
				 $exts = split("[/\\.]", $filename) ; 
				 $n = count($exts)-1; 
				 $exts = $exts[$n]; 
				 return $exts; 
			 }
			 
			//check certification
			if(isset($_GET['certified'])){
				$cert = $_GET['certified'];
				
				$ext = findexts($cert) ;
				$ext = strtoupper($ext);
				$status = 0;
				$remark = 'Failed! - But Certified By User';
				
				//save record to database
				$query = mysql_query("INSERT INTO scrubbing (filename,type,status,remark) VALUES ('$cert','$ext','$status','$remark')");
				if($query){
					$msg = "<div class='alert alert-success'>Processed</div>";
				} else {
					$msg = "<div class='alert alert-danger'>Error!</div>";
				}
			}
						
			if(isset($_POST['process'])){
				//passport upload
				$f_name=$_FILES['file']['name'];
				$f_type=$_FILES['file']['type'];
				$f_size=$_FILES['file']['size'];
				$f_temp=$_FILES['file']['tmp_name'];
				$f_error=$_FILES['file']['error'];
				
				// Picture Image Contraints
				if(!$f_temp==true){
					$msg = "<div class='alert alert-info'>No Selected File, Please Select File</div>";
				} elseif($f_size > 209715200){
					$msg = "<div class='alert alert-info'>File too large</div>";
				} elseif($f_error==1){
					$msg = "<div class='alert alert-danger'>Failed to upload file</div>";
				} else {
					$ext = findexts($f_name) ;
					
					//check extension file
					$chk = mysql_query("SELECT * FROM permission WHERE ext='$ext'");
					if(mysql_num_rows($chk) > 0){
						$ext = strtoupper($ext);
						$status = 1;
						$remark = 'Certified';
						
						//save record to database
						$query = mysql_query("INSERT INTO scrubbing (filename,type,status,remark) VALUES ('$f_name','$ext','$status','$remark')");
						if($query){
							$msg = "<div class='alert alert-success'>Processed</div>";
						} else {
							$msg = "<div class='alert alert-danger'>Error!</div>";
						}
					} else {
						$msg = '
							<div class="alert alert-info">File does not meet Protocol Scrubbing Rule, do you wish to Certify it? <a href="index.php" class="btn btn-warning">No</a> <a href="index.php?certified='.$f_name.'" class="btn btn-success">Yes</a> </div>
						';
					}
				}	
			}
			
			$dir = '';
			$pull = mysql_query("SELECT * FROM scrubbing");
			if(mysql_num_rows($pull) > 0){
				while($pullr = mysql_fetch_assoc($pull)){
					$stat = $pullr['status'];
					if($stat == 1){
						$bg = 'alert alert-success';
						$statu = 'Passed';
					} else {
						$bg = 'alert alert-warning';
						$statu = 'Trial';
					}
					
					$dir .= '
						<tr class="'.$bg.'">
							<td>'.$pullr['filename'].'</td>
							<td>'.$pullr['type'].'</td>
							<td>'.$statu.'</td>
							<td>'.$pullr['remark'].'</td>
						</tr>
					';	
				}
			}
		?>
        <form action="index.php" method="post" enctype="multipart/form-data">
        	Selec File To Process<br />
            <input type="file" name="file" class="form-control" /><br />
            <a href="set.php" class="btn btn-primary">&lArr; Set Permission</a>
            <input type="submit" name="process" value="Perform Protocol Scrubbing" class="btn btn-success" />
        </form><br />
        <?php if(!empty($msg)){echo $msg;} ?>
    </div>
    
    <hr />
    
    <div>
    	<table class="tb">
            <tr class="top">
                <td>File Protocol</td>
                <td>Type</td>
                <td>Status</td>
                <td>Remark</td>
            </tr>
            <?php echo $dir; ?>
        </table>
    </div>
    <br /><br />
    
    <hr />
    
    <footer class="text-center">
    	Copyright &copy; 2015
    </footer>
</body>
</html>