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
    
    <div class="col-lg-12">
    	<h2>Set Permissions</h2>
        <hr />
    </div>
    
    
    <div class="row">
    	<div class="col-lg-4">
        	<?php
				$dir = '';
				$msg = '';
				
				//check delete
				if(isset($_GET['id'])){
					$del_id = $_GET['id'];
					if(mysql_query("DELETE FROM permission WHERE id='$del_id' LIMIT 1")){}
				}
				
				if(isset($_POST['btnExt'])){
					$ext = $_POST['ext'];
					
					if(!$ext){
						$msg = '<div class="alert alert-danger">Ext. is required</div>';
					} else {
						$chk = mysql_query("SELECT * FROM permission WHERE ext='$ext' LIMIT 1");
						if(mysql_num_rows($chk) > 0){
							$msg = '<div class="alert alert-info">Ext. already exist</div>';
						} else {
							$query = mysql_query("INSERT INTO permission (ext) VALUES ('$ext')");
							if($query){
								$msg = '<div class="alert alert-success">Successful</div>';
							} else {
								$msg = '<div class="alert alert-danger">There is problem this time, try again</div>';
							}
						}
					}
				}
				
				//query records
				$pull = mysql_query("SELECT * FROM permission");
				if(mysql_num_rows($pull) <= 0){
					$dir = '<div class="alert alert-info">No records yet</div>';
				} else {
					while($pullr = mysql_fetch_assoc($pull)){
						$dir .= '
							<tr>
								<td>'.$pullr['ext'].'</td>
								<td width="100px"><a href="set.php?id='.$pullr['id'].'">Remove</a></td>
							</tr>
						';	
					}
				}
			?>
            
            <?php echo $msg; ?>
            <form action="set.php" method="post">
            	<label><b>Ext.</b></label><br />
                <input type="text" name="ext" class="form-control" /><br />
                <input type="submit" name="btnExt" value="Add Permission" class="btn btn-success" />
            </form>
        </div>
        
        <div class="col-lg-8">
        	<table class="tb">
            	<tr class="top">
                	<td>Ext.</td>
                    <td>Action</td>
                </tr>
                <?php echo $dir; ?>
            </table>
        </div>
    </div>
    
    <hr />
    
    <footer class="text-center">
    	Copyright &copy; 2015
    </footer>
</body>
</html>