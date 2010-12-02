<?php
//this script runs entire installation process in 5 steps

//take "step" variable to determine which step the current is
$step = $_POST['step'];


//perform field validation(steps 3-5) and database connection tests (steps 3 and 4) and send back to previous step if not working
$errorMessage = array();
if ($step == "3"){
	//first, validate all fields are filled in
	$database_host = trim($_POST['database_host']);
	$database_username = trim($_POST['database_username']);
	$database_password = trim($_POST['database_password']);
	$database_name = trim($_POST['database_name']);

	if (!$database_host) $errorMessage[] = 'Host name is required';
	if (!$database_name) $errorMessage[] = 'Database name is required';
	if (!$database_username) $errorMessage[] = 'User name is required';
	if (!$database_password) $errorMessage[] = 'Password is required';

	//only continue to checking DB connections if there were no errors this far
	if (count($errorMessage) > 0){
		$step="2";
	}else{

		//first check connecting to host
		$link = @mysql_connect("$database_host", "$database_username", "$database_password");
		if (!$link) {
			$errorMessage[] = "Could not connect to the server '" . $database_host . "'<br />MySQL Error: " . mysql_error();
		}else{

			//next check that the database exists
			$dbcheck = @mysql_select_db("$database_name");
			if (!$dbcheck) {
				$errorMessage[] = "Unable to access the database '" . $database_name . "'.  Please verify it has been created.<br />MySQL Error: " . mysql_error();
			}else{
				//make sure the tables don't already exist - otherwise this script will overwrite all of the data!
				$query = "SELECT count(*) count FROM information_schema.`COLUMNS` WHERE table_schema = '" . $database_name . "' AND table_name='License'";

				//if License table exists, error out
				if (!$row = mysql_fetch_array(mysql_query($query))){
					$errorMessage[] = "Please verify your database user has access to select from the information_schema MySQL metadata database.";
				}else{
					if ($row['count'] > 0){
						$errorMessage[] = "The Usage Statistics tables already exist.  If you intend to upgrade, please run upgrade.php instead.  If you would like to perform a fresh install you will need to manually drop all of the Usage Statistics tables in this schema first.";
					}else{
						//passed db host, name check, can open/run file now
						//make sure SQL file exists
						$test_sql_file = "test_create.sql";
						$sql_file = "create_tables_data.sql";

						if (!file_exists($test_sql_file)) {
							$errorMessage[] = "Could not open sql file: " . $test_sql_file . ".  If this file does not exist you must download new install files.";
						}else{
							//run the file - checking for errors at each SQL execution
							$f = fopen($test_sql_file,"r");
							$sqlFile = fread($f,filesize($test_sql_file));
							$sqlArray = explode(";",$sqlFile);



							//Process the sql file by statements
							foreach ($sqlArray as $stmt) {
							   if (strlen(trim($stmt))>3){
									//replace the DATABASE_NAME parameter with what was actually input
									$stmt = str_replace("_DATABASE_NAME_", $database_name, $stmt);

									$result = mysql_query($stmt);
									if (!$result){
										$errorMessage[] = mysql_error() . "<br /><br />For statement: " . $stmt;
										 break;
									}
								}
							}

						}


						//once this check has passed we can run the entire ddl/dml script
						if (count($errorMessage) == 0){
							if (!file_exists($sql_file)) {
								$errorMessage[] = "Could not open sql file: " . $sql_file . ".  If this file does not exist you must download new install files.";
							}else{
								//run the file - checking for errors at each SQL execution
								$f = fopen($sql_file,"r");
								$sqlFile = fread($f,filesize($sql_file));
								$sqlArray = explode(';',$sqlFile);



								//Process the sql file by statements
								foreach ($sqlArray as $stmt) {
								   if (strlen(trim($stmt))>3){
										//replace the DATABASE_NAME parameter with what was actually input
										$stmt = str_replace("_DATABASE_NAME_", $database_name, $stmt);

										$result = mysql_query($stmt);
										if (!$result){
											$errorMessage[] = mysql_error() . "<br /><br />For statement: " . $stmt;
											 break;
										}
									}
								}

							}
						}

					}
				}
			}
		}

	}

	if (count($errorMessage) > 0){
		$step="2";
	}

}else if ($step == "4"){

	//first, validate all fields are filled in
	$database_host = trim($_POST['database_host']);
	$database_username = trim($_POST['database_username']);
	$database_password = trim($_POST['database_password']);
	$database_name = trim($_POST['database_name']);
	$admin_login = trim($_POST['admin_login']);

	if (!$database_username) $errorMessage[] = 'User name is required';
	if (!$database_password) $errorMessage[] = 'Password is required';
	if (!$admin_login) $errorMessage[] = 'Admin user is required';

	//only continue to checking DB connections if there were no errors this far
	if (count($errorMessage) > 0){
		$step="3";
	}else{

		//first check connecting to host
		$link = @mysql_connect("$database_host", "$database_username", "$database_password");
		if (!$link) {
			$errorMessage[] = "Could not connect to the server '" . $database_host . "'<br />MySQL Error: " . mysql_error();
		}else{

			//next check that the database exists
			$dbcheck = @mysql_select_db("$database_name");
			if (!$dbcheck) {
				$errorMessage[] = "Unable to access the database '" . $database_name . "'.  Please verify it has been created.<br />MySQL Error: " . mysql_error();
			}else{
				//passed db host, name check, test that user can select from License database
				$result = mysql_query("SELECT privilegeID FROM " . $database_name . ".Privilege WHERE shortName like '%admin%';");
				if (!$result){
					$errorMessage[] = "Unable to select from the Privilege table in database '" . $database_name . "' with user '" . $database_username . "'.  Error: " . mysql_error();
				}else{
					while ($row = mysql_fetch_array($result, MYSQL_NUM)) {
						$privilegeID = $row[0];
					}

					//delete admin user if they exist, then set them back up
					$query = "DELETE FROM " . $database_name . ".User WHERE loginID = '" . $admin_login . "';";
					mysql_query($query);
					$query = "INSERT INTO " . $database_name . ".User (loginID, privilegeID) values ('" . $admin_login . "', " . $privilegeID . ");";
					mysql_query($query);
				}

			}
		}

	}

	if (count($errorMessage) > 0){
		$step="3";
	}


}else if ($step == "5"){

	//first, validate all required fields are filled in
	$remoteAuthVariableName = trim($_POST['remoteAuthVariableName']);
	$organizationsModule = $_POST['organizationsModule'];
	$cancellationModule = $_POST['cancellationModule'];
	$licensingModule = $_POST['licensingModule'];
	$reportingModule = $_POST['reportingModule'];
	$useOutliers = $_POST['useOutliers'];
	$organizationsDatabaseName = trim($_POST['organizationsDatabaseName']);
	$base_url = trim($_POST['base_url']);


	$database_host = $_POST['database_host'];
	$database_name = $_POST['database_name'];
	$database_username = $_POST['database_username'];
	$database_password = trim($_POST['database_password']);

	if (!$remoteAuthVariableName) {
		$errorMessage[] = 'Remote Auth Variable Name is required';
	}else{
		//replace double quote with single quote since config writes with double quote
		$remoteAuthVariableName = str_replace('"', "'", $remoteAuthVariableName);

		//make sure variable name has matched number of ', otherwise it will bomb the program
		if((substr_count($remoteAuthVariableName, "'") % 2)!==0){
			$errorMessage[] = 'Make sure Remote Auth Variable Name has matched single or double quotes';
		}

	}
	if ((!$organizationsDatabaseName) && ($_POST['organizationsModule'])) $errorMessage[] = "If you are using organizations module you must enter the organizations module database name.  It doesn't need to be created yet.";

	//test the base url if entered
	if (($base_url) && ($base_url != 'http://')){
		if (strpos($base_url, 'http://') === false){
			$base_url = 'http://' . $base_url;
		}

		$test_url = @parse_url($base_url);
		if (!urlExists($base_url)) {
			$errorMessage[] = 'Base URL is invalid';
		}
	}


	//only continue to checking DB connections if there were no errors this far
	if (count($errorMessage) > 0){
		$step="4";
	}else{

		//write the config file
		$configFile = "../admin/configuration.ini";
		$fh = fopen($configFile, 'w');

		if (!$fh){
			$errorMessage[] = "Could not open file " . $configFile . ".  Please verify you can write to the /admin/ directory.";
		}else{
			if (!$organizationsModule) $organizationsModule = "N";
			if (!$cancellationModule) $cancellationModule = "N";
			if (!$licensingModule) $licensingModule = "N";
			if (!$resourcesModule) $resourcesModule = "N";
			if (!$reportingModule) $reportingModule = "N";
			if (!$useOutliers) $useOutliers = "N";


			$iniData = array();
			$iniData[] = "[settings]";
			$iniData[] = "organizationsModule=" . $organizationsModule;
			$iniData[] = "organizationsDatabaseName=" . $organizationsDatabaseName;
			$iniData[] = "cancellationModule=" . $cancellationModule;
			$iniData[] = "licensingModule=" . $licensingModule;
			$iniData[] = "resourcesModule=" . $resourcesModule;
			$iniData[] = "reportingModule=" . $reportingModule;
			$iniData[] = "useOutliers=" . $useOutliers;
			$iniData[] = "baseURL = \"" . $base_url . "\"";
			$iniData[] = "remoteAuthVariableName=\"" . $remoteAuthVariableName . "\"";

			$iniData[] = "\n\n[database]";
			$iniData[] = "type = \"mysql\"";
			$iniData[] = "host = \"" . $database_host . "\"";
			$iniData[] = "name = \"" . $database_name . "\"";
			$iniData[] = "username = \"" . $database_username . "\"";
			$iniData[] = "password = \"" . $database_password . "\"";

			fwrite($fh, implode("\n",$iniData));
			fclose($fh);
		}


	}

	if (count($errorMessage) > 0){
		$step="4";
	}


}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>CORAL Installation</title>
<link rel="stylesheet" href="css/style.css" type="text/css" />
</head>
<body>
<center>
<table style='width:700px;'>
<tr>
<td style='vertical-align:top;'>
<div style="text-align:left;">


<?php if(!$step){ ?>

	<h3>Welcome to a new CORAL Usage Statistics installation!</h3>
	This installation will:
	<ul>
		<li>Check that you are running PHP 5</li>
		<li>Connect to MySQL and create the CORAL Usage Statistics tables</li>
		<li>Test the database connection the CORAL Usage Statistics application will use </li>
		<li>Set up the config file with settings you choose</li>
	</ul>

	<br />
	To get started you should:
	<ul>
		<li>Create a MySQL Schema for CORAL Usage Statistics Module - recommended name is coral_usage_prod.  Each CORAL module has separate user permissions and requires a separate schema.</li>
		<li>Know your host, username and password for MySQL with permissions to create tables</li>
		<li>It is recommended for security to have a different username and password for CORAL with only select, insert, update and delete privileges to CORAL schemas</li>
		<li>The server variable to access your school's auth system via PHP - for example $HTTP_SERVER_VARS['REMOTE_USER'] or $SERVER['AUTH_USER']</li>
		<li>Know what other systems you will be using operating with - you will be asked whether you are using the Usage Statistics Reporting Module or the Organizations Module.  If you are using the Organizations module you will need to provide the name of the database/schema used for Organizations for inter-operability.  Recommended name is coral_organizations_prod.  For more information about inter-operability refer to the user guide.</li>
		<li>Verify that your /admin/ directory is writable by server during the installation process (chmod 777).  After installation you should chmod it back.</li>
		<li>Verify that your /archive/ and /log/ directories are writable by the server - the CORAL Usage Statistics Module will write files to these directories.</li>
	</ul>


	<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
	<input type='hidden' name='step' value='1'>
	<input type="submit" value="Continue" name="submit">
	</form>


<?php
//first step - check system info and verify php 5
} else if ($step == '1') {
	ob_start();
    phpinfo(-1);
    $phpinfo = array('phpinfo' => array());
    if(preg_match_all('#(?:<h2>(?:<a name=".*?">)?(.*?)(?:</a>)?</h2>)|(?:<tr(?: class=".*?")?><t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>(?:<t[hd](?: class=".*?")?>(.*?)\s*</t[hd]>)?)?</tr>)#s', ob_get_clean(), $matches, PREG_SET_ORDER))
    foreach($matches as $match){
        if(strlen($match[1]))
            $phpinfo[$match[1]] = array();
        elseif(isset($match[3]))
            $phpinfo[end(array_keys($phpinfo))][$match[2]] = isset($match[4]) ? array($match[3], $match[4]) : $match[3];
        else
            $phpinfo[end(array_keys($phpinfo))][] = $match[2];
    }




    ?>

	<h3>Getting system info and verifying php version</h3>
	<ul>
	<li>System: <?=$phpinfo['phpinfo']['System'];?></li>
    <li>PHP version: <?=phpversion();?></li>
    <li>Server API: <?=$phpinfo['phpinfo']['Server API'];?></li>
	</ul>

	<br />

	<?php


	if (phpversion() >= 5){
	?>
		<form action="<?=$_SERVER['PHP_SELF']?>" method="post">
		<input type='hidden' name='step' value='2'>
		<input type="submit" value="Continue" name="submit">
		</form>
	<?php
	}else{
		echo "<span style='font-size=115%;color:red;'>PHP 5 is not installed on this server!  Installation will not continue.</font>";
	}

//second step - ask for DB info to run DDL
} else if ($step == '2') {

	if (!$database_host) $database_host='localhost';
	if (!$database_name) $database_name='coral_usage_prod';
	?>
		<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<h3>MySQL info with permissions to create tables</h3>
		<?php
			if (count($errorMessage) > 0){
				echo "<span style='color:red'><b>The following errors occurred:</b><br /><ul>";
				foreach ($errorMessage as $err)
					echo "<li>" . $err . "</li>";
				echo "</ul></span>";
			}
		?>
		<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr>
			<tr>
				<td>&nbsp;Database Host</td>
				<td>
					<input type="text" name="database_host" value='<?=$database_host?>' size="30">
				</td>
			</tr>
			<tr>
				<td>&nbsp;Database Schema Name</td>
				<td>
					<input type="text" name="database_name" size="30" value="<?=$database_name?>">
				</td>
			</tr>
			<tr>
				<td>&nbsp;Database Username</td>
				<td>
					<input type="text" name="database_username" size="30" value="<?=$database_username?>">
				</td>
			</tr>
			<tr>
				<td>&nbsp;Database Password</td>
				<td>
					<input type="text" name="database_password" size="30" value="<?=$database_password?>">
				</td>
			</tr>
			<tr>
				<td colspan=2>&nbsp;</td>
			</tr>
			<tr>
				<td align='left'>&nbsp;</td>
				<td align='left'>
				<input type='hidden' name='step' value='3'>
				<input type="submit" value="Continue" name="submit">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="Cancel" onclick="document.location.href='index.php'">
				</td>
			</tr>

		</table>
		</form>
<?php
//third step - ask for DB info to log in from CORAL
} else if ($step == '3') {

	?>
		<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<h3>MySQL user for CORAL web application - with select, insert, update, delete privileges to CORAL schemas</h3>
		*It's recommended but not required that this user is different than the one used on the prior step
		<?php
			if (count($errorMessage) > 0){
				echo "<br /><span style='color:red'><b>The following errors occurred:</b><br /><ul>";
				foreach ($errorMessage as $err)
					echo "<li>" . $err . "</li>";
				echo "</ul></span>";
			}
		?>
		<input type="hidden" name="database_host" value='<?=$database_host?>'>
		<input type="hidden" name="database_name" value="<?=$database_name?>">

		<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr>
			<tr>
				<td>&nbsp;Database Username</td>
				<td>
					<input type="text" name="database_username" size="30" value="<?=$database_username?>">
				</td>
			</tr>
			<tr>
				<td>&nbsp;Database Password</td>
				<td>
					<input type="text" name="database_password" size="30" value="<?=$database_password?>">
				</td>
			</tr>

			<tr>
				<td colspan="2"><br />&nbsp;Additionally, since user privileges are driven through the web, we will need to set up the first admin account to administer other users.  Please enter your externally authenticated Login ID below.</td>
			</tr>
			<tr>
				<td>&nbsp;Your Login ID</td>
				<td>
					<input type="text" name="admin_login" size="30" value="<?=$admin_login?>">
				</td>
			</tr>
			<tr>
				<td colspan=2>&nbsp;</td>
			</tr>
			<tr>
				<td align='left'>&nbsp;</td>
				<td align='left'>
				<input type='hidden' name='step' value='4'>
				<input type="submit" value="Continue" name="submit">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="Cancel" onclick="document.location.href='index.php'">
				</td>
			</tr>

		</table>
		</form>
<?php
//fourth step - ask for other settings in configuration.ini
} else if ($step == '4') {
	if (!$remoteAuthVariableName) $remoteAuthVariableName = "HTTP_SERVER_VARS['REMOTE_USER']";
	if ($_POST['organizationsModule']) $organizationsChecked = "checked";
	if ($_POST['cancellationModule']) $cancellationChecked = "checked";
	if ($_POST['licensingModule']) $licensingChecked = "checked";
	if ($_POST['reportingModule']) $reportingModule = "checked";
	if ($_POST['resourcesChecked']) $resourcesChecked = "checked";
	if ($_POST['useOutliers']) $useOutliersChecked = "checked";

	?>
		<form method="post" action="<?=$_SERVER['PHP_SELF']?>">
		<h3>Inter-operability and other config settings</h3>
		<?php
			if (count($errorMessage) > 0){
				echo "<span style='color:red'><b>The following errors occurred:</b><br /><ul>";
				foreach ($errorMessage as $err)
					echo "<li>" . $err . "</li>";
				echo "</ul></span>";
			}
		?>
		<input type="hidden" name="database_host" value='<?=$database_host?>'>
		<input type="hidden" name="database_name" value="<?=$database_name?>">
		<input type="hidden" name="database_username" value='<?=$database_username?>'>
		<input type="hidden" name="database_password" value="<?=$database_password?>">

		<table width="100%" border="0" cellspacing="0" cellpadding="2">
		<tr>
			<tr>
				<td>&nbsp;Are you using the usage statistics reporting module?</td>
				<td>
					<input type="checkbox" name="reportingModule" value="Y" <?=$reportingModule?>>
				</td>
			</tr>
			<tr>
				<td>&nbsp;Are you using organizations module?</td>
				<td>
					<input type="checkbox" name="organizationsModule" value="Y" <?=$organizationsChecked?>>
				</td>
			</tr>
			<tr>
				<td>&nbsp;&nbsp;&nbsp;If so, enter organizations database schema name</td>
				<td>
					<input type="text" name="organizationsDatabaseName" style="width:250px;" value="<?=$organizationsDatabaseName?>">
				</td>
			</tr>
			<tr>
				<td>&nbsp;Are you using cancellation module?</td>
				<td>
					<input type="checkbox" name="cancellationModule" value="Y" <?=$cancellationChecked?>>
				</td>
			</tr>
			<tr>
				<td>&nbsp;Are you using the licensing module?</td>
				<td>
					<input type="checkbox" name="licensingModule" value="Y" <?=$licensingChecked?>>
				</td>
			</tr>
			<tr>
				<td>&nbsp;Are you going to use the outlier flagging feature<br />&nbsp; when importing statistics?</td>
				<td>
					<input type="checkbox" name="useOutliers" value="Y" <?=$useOutliersChecked?>>
				</td>
			</tr>

			<tr>
				<td>&nbsp;Link Resolver Base URL (optional)</td>
				<td>
					<textarea id="base_url" name="base_url" style="width:250px;" rows="3"><?=$base_url?></textarea>
				</td>
			</tr>

			<tr>
				<td>&nbsp;Remote Auth Variable Name</td>
				<td>
					<input type="text" name="remoteAuthVariableName" style="width:250px;" value="<?=$remoteAuthVariableName?>">
				</td>
			</tr>
			<tr>
				<td colspan=2>&nbsp;</td>
			</tr>
			<tr>
				<td align='left'>&nbsp;</td>
				<td align='left'>
				<input type='hidden' name='step' value='5'>
				<input type="submit" value="Continue" name="submit">
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="button" value="Cancel" onclick="document.location.href='index.php'">
				</td>
			</tr>

		</table>
		</form>
<?php
}else if ($step == '5'){ ?>
	<h3>CORAL Usage Statistics installation is now complete!</h3>
	It is recommended you now:
	<ul>
		<li>Set up your .htaccess file</li>
		<li>Remove the /install/ directory for security purposes</li>
		<li>Set up your users on the <a href='../admin.php'>admin screen</a>.</li>
	</ul>

<?php
}


function urlExists($url) {
      $hdrs = @get_headers($url);
      return is_array($hdrs) ? preg_match('/^HTTP\\/\\d+\\.\\d+\\s+2\\d\\d\\s+.*$/',$hdrs[0]) : false;
}
?>

</td>
</tr>
</table>
<br />
</center>


</body>
</html>