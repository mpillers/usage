<?php
/*
**************************************************************************************************************************
** CORAL Usage Statistics Module v. 1.0
**
** Copyright (c) 2010 University of Notre Dame
**
** This file is part of CORAL.
**
** CORAL is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
**
** CORAL is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License along with CORAL.  If not, see <http://www.gnu.org/licenses/>.
**
**************************************************************************************************************************
**************************************************************************************************************************
** ajax_processing.php contains processing (adds/updates/deletes) on data sent using ajax from forms and other pages
**
** when ajax_processing.php is called through ajax, 'action' parm is required to dictate which form will be returned
**
**************************************************************************************************************************
*/


include "common.php";
include_once 'directory.php';

$action = $_REQUEST['action'];

switch ($action) {


	//log email addresses (on admin page)
	case 'getLogEmailAddressForm':

		if (isset($_GET['logEmailAddressID']) && ($_GET['logEmailAddressID'] != '')){
			$logEmailAddressID = $_GET['logEmailAddressID'];
			$addUpdate = 'Update';
			$logEmailAddress = new LogEmailAddress(new NamedArguments(array('primaryKey' => $_GET['logEmailAddressID'])));
		}else{
			$logEmailAddressID = '';
			$addUpdate = 'Add';
			$logEmailAddress = new LogEmailAddress();
		}




		?>
		<div id='div_updateForm'>
		<input type='hidden' id='updateLogEmailAddressID' name='updateLogEmailAddressID' value='<?php echo $logEmailAddressID; ?>'>
		<table class="thickboxTable" style="width:230px;">
		<tr>
		<td colspan='2'><br /><span class='headerText'><?php echo $addUpdate; ?> Email Address</span><br /></td>
		</tr>
		<tr>
		<td>
		<input type='text' id='emailAddress' name='emailAddress' value='<?php if (isset($_GET['logEmailAddressID']) && ($_GET['logEmailAddressID'] != '')) echo $logEmailAddress->emailAddress; ?>' style='width:190px;'/>
		</td>
		<td>
		<a href='javascript:doSubmitLogEmailAddress();'><?php echo strtolower($addUpdate); ?></a>
		</td>
		</tr>
		<tr>
		<td colspan='2'><p><a href='#' onclick='window.parent.tb_remove(); return false'>close</a></td>
		</tr>
		</table>
		</div>


		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#emailAddress').keyup(function(e) {

				   if(e.keyCode == 13) {
					   doSubmitLogEmailAddress();
				   }
        	});

        </script>


		<?php

		break;

	case 'getOutlierForm':


		if (isset($_GET['outlierID']) && ($_GET['outlierID'] != '')){
			$outlierID = $_GET['outlierID'];
			$outlier = new Outlier(new NamedArguments(array('primaryKey' => $_GET['outlierID'])));
		}


		?>
		<div id='div_updateForm'>
		<input type='hidden' id='updateOutlierID' name='updateOutlierID' value='<?php echo $outlierID; ?>'>
		<table class="thickboxTable" style="width:300px;padding:2px;">
		<tr><td colspan='2'><span class='headerText'>Update Outlier - <b>Level <?php echo $outlier->outlierLevel; ?></b><br /><br /></td></tr>
		<tr><td style='vertical-align:top;text-align:right;'><label for='overageCount'><b>Count Over</b></label</td><td><input type='text' id='overageCount' name='overageCount' value="<?php echo $outlier->overageCount; ?>" style='width:140px;' /><span id='span_error_overageCount' style='color:red'></span></td></tr>
		<tr><td style='vertical-align:top;text-align:right;'><label for='overagePercent'><b>% Over prior 12 months</b></label</td><td><input type='text' id='overagePercent' name='overagePercent' value="<?php echo $outlier->overagePercent; ?>" style='width:140px;' /><span id='span_error_overagePercent' style='color:red'></span></td></tr>

		<tr style="vertical-align:middle;">
		<td style="padding-top:8px;text-align:right;">&nbsp;</td>
		<td style="padding-top:18px;padding-right:8px;text-align:left;"><input type='button' value='Update' onclick='javascript:window.parent.updateOutlier();'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='cancel' onclick="window.parent.tb_remove(); return false"></td>
		</tr>

		</table>

		</div>

		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
			$('#overageCount').keyup(function(e) {
				  if(e.keyCode == 13) {
					window.parent.updateOutlier();
				  }
			});


			//do submit if enter is hit
			$('#overagePercent').keyup(function(e) {
				  if(e.keyCode == 13) {
					window.parent.updateOutlier();
				  }
			});

        </script>

		<?php

		break;




	//reporting display name (on reporting page)
	case 'getReportDisplayForm':
		if (isset($_GET['updateID'])) $updateID = $_GET['updateID']; else $updateID = '';

		if ($_GET['type'] == 'platform'){
			$obj = new Platform(new NamedArguments(array('primaryKey' => $updateID)));
		}else{
			$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $updateID)));
		}


		?>
		<div id='div_updateForm'>
		<input type='hidden' id='updateID' name='updateID' value='<?php echo $updateID; ?>'>
		<input type='hidden' id='type' name='type' value='<?php echo $_GET['type']; ?>'>
		<table class="thickboxTable" style="width:230px;">
		<tr>
		<td colspan='2'><br /><span class='headerText'>Update Report Display Name</span><br /></td>
		</tr>
		<tr>
		<td>
		<?php
		echo "<input type='text' id='reportDisplayName' name='reportDisplayName' value='" . $obj->reportDisplayName . "' style='width:190px;'/></td><td><a href='javascript:updateReportDisplayName();'>update</a>";
		?>


		</td>
		</tr>
		<tr>
		<td colspan='2'><p><a href='#' onclick='window.parent.tb_remove(); return false'>close</a></td>
		</tr>
		</table>
		</div>


		<script type="text/javascript">
		   //attach enter key event to new input and call add data when hit
		   $('#reportDisplayName').keyup(function(e) {

				   if(e.keyCode == 13) {
					   updateReportDisplayName();
				   }
        	});

        </script>


		<?php

		break;


	case 'getPlatformNoteForm':
		if (isset($_GET['platformNoteID'])) $platformNoteID = $_GET['platformNoteID']; else $platformNoteID = '';
 		if (isset($_GET['platformID'])) $platformID = $_GET['platformID'];


		if ($platformNoteID) $addUpdate = 'Update'; else $addUpdate = 'Add';

		if ($platformNoteID){
			$platformNote = new PlatformNote(new NamedArguments(array('primaryKey' => $platformNoteID)));

			$platformID = $platformNote->platformID;

			if ($platformNote->counterCompliantInd == '1'){
				$counterCompliant = 'checked';
				$notCounterCompliant = '';
			}elseif ($platformNote->counterCompliantInd == '0'){
				$notCounterCompliant = 'checked';
				$counterCompliant = '';
			}else{
				$notCounterCompliant = '';
				$counterCompliant = '';
			}

			if (($platformNote->endYear == '0') || ($platformNote->endYear =='')) $endYear = ''; else $endYear = $platformNote->endYear;
			$startYear = $platformNote->startYear;
			$noteText = $platformNote->noteText;

		}else{
			$platformNote = new PlatformNote();
			$notCounterCompliant = '';
			$counterCompliant = '';
			$startYear = '';
			$endYear = '';
			$noteText = '';
		}



		?>
		<div id='div_updateForm'>
		<input type='hidden' id='editPlatformNoteID' name='editPlatformNoteID' value='<?php echo $platformNoteID; ?>'>
		<input type='hidden' id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		<table class="thickboxTable" style="width:400px;padding:2px;">
			<tr>
				<td colspan='2'><span class='headerText'><?php echo $addUpdate; ?> Interface Notes</span><span id='span_errors' style='color:red;'><br /></span><br /></td>
			</tr>
			<tr>
				<td style='vertical-align:top;text-align:right;width:135px;'><label for='startYear'><b>Start Year:</b></label</td>
				<td><input type='text' id='startYear' name='startYear' value="<?php echo $platformNote->startYear; ?>" style='width:90px;' /><span id='span_error_startYear' style='color:red'></span></td>
			</tr>
			<tr>
				<td style='vertical-align:top;text-align:right;'><label for='endYear'><b>End Year:</b></label</td>
				<td><input type='text' id='endYear' name='endYear' value="<?php echo $endYear; ?>" style='width:90px;' /><span id='span_error_endYear' style='color:red'></span></td>
			</tr>
			<tr>
				<td style='vertical-align:top;text-align:right;'><label for='counterCompliantInd'><b>Counter Compliant?</b></label</td>
				<td>
				<input type='radio' id='counterCompliantInd' name='counterCompliantInd' value='1' <?php echo $counterCompliant; ?> />&nbsp;Yes&nbsp;&nbsp;
				<input type='radio' id='counterCompliantInd' name='counterCompliantInd' value='0' <?php echo $notCounterCompliant; ?> />&nbsp;No

				</td>
			</tr>

			<tr>
				<td style='vertical-align:top;text-align:right;'><label for='noteText'><b>Interface Notes:</b></label></td>
				<td><textarea cols='36' rows='4' id='noteText' name='noteText' style='width:250px;'><?php echo $noteText; ?></textarea></td>
			</tr>

			<tr style="vertical-align:middle;">
				<td style="padding-top:8px;text-align:right;">&nbsp;</td>
				<td style="padding-top:8px;padding-right:8px;">
					<table class='noBorderTable' style='width:100%;'>
						<tr>
							<td style='text-align:left'><input type='button' value='submit' name='submitPlatformNoteForm' id ='submitPlatformNoteForm'></td>
							<td style='text-align:right'><input type='button' value='cancel' onclick="tb_remove()"></td>
						</tr>
					</table>
				</td>
			</tr>

		</table>

		</div>

		<script type="text/javascript" src="js/forms/platformNoteSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

		break;








	case 'getPublisherNoteForm':
		if (isset($_GET['publisherPlatformNoteID'])) $publisherPlatformNoteID = $_GET['publisherPlatformNoteID']; else $publisherPlatformNoteID = '';
 		if (isset($_GET['publisherPlatformID'])) $publisherPlatformID = $_GET['publisherPlatformID'];

		if ($publisherPlatformNoteID){
			$addUpdate = 'Update';

			$publisherPlatformNote = new PublisherPlatformNote(new NamedArguments(array('primaryKey' => $publisherPlatformNoteID)));

			$publisherPlatformID = $publisherPlatformNote->publisherPlatformID;

			if (($publisherPlatformNote->endYear == '0') || ($publisherPlatformNote->endYear =='')) $endYear = ''; else $endYear = $publisherPlatformNote->endYear;

		}else{
			$addUpdate = 'Add';
			$publisherPlatformNote = new PublisherPlatformNote();
		}


		?>
		<div id='div_updateForm'>
		<input type='hidden' id='editPublisherPlatformNoteID' name='editPublisherPlatformNoteID' value='<?php echo $publisherPlatformNoteID; ?>'>
		<input type='hidden' id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>
		<table class="thickboxTable" style="width:310px;padding:2px;">
			<tr>
				<td colspan='2'><span class='headerText'><?php echo $addUpdate; ?> Publisher Notes</span><span id='span_errors' style='color:red;'><br /></span><br /></td>
			</tr>
			<tr>
				<td style='vertical-align:top;text-align:right;width:135px;'><label for='startYear'><b>Start Year:</b></label</td>
				<td><input type='text' id='startYear' name='startYear' value="<?php echo $publisherPlatformNote->startYear; ?>" style='width:90px;' /><span id='span_error_startYear' style='color:red'></span></td>
			</tr>
			<tr>
				<td style='vertical-align:top;text-align:right;'><label for='endYear'><b>End Year:</b></label</td>
				<td><input type='text' id='endYear' name='endYear' value="<?php echo $endYear; ?>" style='width:90px;' /><span id='span_error_endYear' style='color:red'></span></td>
			</tr>
			<tr>
				<td style='vertical-align:top;text-align:right;'><label for='noteText'><b>Publisher Notes:</b></label></td>
				<td><textarea cols='36' rows='4' id='noteText' name='noteText' style='width:200px;'><?php echo $publisherPlatformNote->noteText; ?></textarea></td>
			</tr>

			<tr style="vertical-align:middle;">
				<td style="padding-top:8px;text-align:right;">&nbsp;</td>
				<td style="padding-top:8px;padding-right:8px;">
					<table class='noBorderTable' style='width:100%;'>
						<tr>
							<td style='text-align:left'><input type='button' value='submit' name='submitPublisherNoteForm' id ='submitPublisherNoteForm'></td>
							<td style='text-align:right'><input type='button' value='cancel' onclick="tb_remove()"></td>
						</tr>
					</table>
				</td>
			</tr>

		</table>

		</div>

		<script type="text/javascript" src="js/forms/publisherNoteSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

		break;






	case 'getLoginForm':
		if (isset($_GET['externalLoginID'])) $externalLoginID = $_GET['externalLoginID']; else $externalLoginID = '';
 		if (isset($_GET['platformID'])) $platformID = $_GET['platformID']; else $platformID = '';
 		if (isset($_GET['publisherPlatformID'])) $publisherPlatformID = $_GET['publisherPlatformID']; else $publisherPlatformID = '';

		if ($externalLoginID){
			$addUpdate = 'Update';
			$externalLogin = new ExternalLogin(new NamedArguments(array('primaryKey' => $externalLoginID)));

			$publisherPlatformID = $externalLogin->publisherPlatformID;
			$platformID = $externalLogin->platformID;
		}else{
			$addUpdate = 'Add';
			$externalLogin = new ExternalLogin();
		}

		?>
		<div id='div_updateForm'>
		<input type='hidden' id='editExternalLoginID' name='editExternalLoginID' value='<?php echo $externalLoginID; ?>'>
		<input type='hidden' id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		<input type='hidden' id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>
		<table class="thickboxTable" style="width:320px;padding:2px;">
			<tr>
				<td colspan='2'><span class='headerText'><?php echo $addUpdate; ?> Login</span><span id='span_errors' style='color:red;'><br /></span><br /></td>
			</tr>
			<tr>
				<td style='vertical-align:top;text-align:right;width:135px;'><label for='username'><b>Username:</b></label</td>
				<td><input type='text' id='username' name='username' value="<?php if ($externalLoginID) echo $externalLogin->username; ?>" style='width:200px;' /><span id='span_error_loginID' style='color:red'></span></td>
			</tr>
			<tr>
				<td style='vertical-align:top;text-align:right;'><label for='password'><b>Password:</b></label</td>
				<td><input type='text' id='password' name='password' value="<?php if ($externalLoginID) echo $externalLogin->password; ?>" style='width:200px;' /><span id='span_error_password' style='color:red'></span></td>			</tr>
			<tr>
				<td style='vertical-align:top;text-align:right;'><label for='loginURL'><b>URL:</b></label</td>
				<td><input type='text' id='loginURL' name='loginURL' value="<?php if ($externalLoginID) echo $externalLogin->loginURL; ?>" style='width:200px;' /><span id='span_error_url' style='color:red'></span></td>
			</tr>
			<tr>
				<td style='vertical-align:top;text-align:right;'><label for='noteText'><b>Login Notes:</b></label></td>
				<td><textarea cols='36' rows='4' id='noteText' name='noteText' style='width:200px;'><?php if ($externalLoginID) echo $externalLogin->noteText; ?></textarea></td>
			</tr>

			<tr style="vertical-align:middle;">
				<td style="padding-top:8px;text-align:right;">&nbsp;</td>
				<td style="padding-top:8px;padding-right:8px;">
					<table class='noBorderTable' style='width:100%;'>
						<tr>
							<td style='text-align:left'><input type='button' value='submit' name='submitExternalLoginForm' id ='submitExternalLoginForm'></td>
							<td style='text-align:right'><input type='button' value='cancel' onclick="tb_remove()"></td>
						</tr>
					</table>
				</td>
			</tr>

		</table>

		</div>

		<script type="text/javascript" src="js/forms/externalLoginSubmitForm.js?random=<?php echo rand(); ?>"></script>

		<?php

		break;


	//form to edit associated organizations
    case 'getOrganizationForm':

		$publisherPlatformID = $_GET['publisherPlatformID'];
		$platformID = $_GET['platformID'];

		if (isset($_GET['publisherPlatformID']) && ($_GET['publisherPlatformID'] != '')){
			$obj = new PublisherPlatform(new NamedArguments(array('primaryKey' => $_GET['publisherPlatformID'])));
		}else{
			$obj = new Platform(new NamedArguments(array('primaryKey' => $_GET['platformID'])));
		}

		if ($obj->organizationID) $organizationName = $obj->getOrganizationName; else $organizationName = '';

		?>
		<div id='div_organizationsForm'>
		<form id='organizationsForm'>
		<input type='hidden' id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>
		<input type='hidden' id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		<table class="thickboxTable" style="width:260px;">
		<tr>
		<td colspan='2'><span class='headerText'>Link Associated Organization</span><br /><br /></td>
		</tr>

		<tr>
		<td colspan='2'><label for="organizationID" class="formText">Organization:</label>  <span id='span_error_organizationName' class='errorText'></span><br />
		<input type='textbox' id='organizationName' name='organizationName' value="<?php echo $organizationName; ?>" style='width:232px;' />
		<input type='hidden' id='organizationID' name='organizationID' value='<?php echo $obj->organizationID; ?>'>
		<span id='span_error_organizationNameResult' class='errorText'></span>
		<br />
		</td>
		</tr>



		<tr style="vertical-align:middle;">
		<td style="padding-top:8px;"><input type='button' value='submit' name='submitOrganization' id ='submitOrganization'></td>
		<td style="padding-top:8px;padding-right:8px;text-align:right;"><input type='button' value='cancel' onclick="tb_remove()"></td>
		</tr>
		</table>



		<script type="text/javascript" src="js/forms/organizationForm.js?random=<?php echo rand(); ?>"></script>
		</form>
		</div>


		<?php

        break;





	case 'getMonthlyOutlierForm':
		if (isset($_GET['platformID'])) $platformID = $_GET['platformID']; else $platformID = '';
 		if (isset($_GET['publisherPlatformID'])) $publisherPlatformID = $_GET['publisherPlatformID'];

		$archiveInd = $_GET['archiveInd'];
		$year = $_GET['year'];
		$month = $_GET['month'];


		$statsArray = array();
		if ($publisherPlatformID) {
			$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
			$publisher = new Publisher(new NamedArguments(array('primaryKey' => $publisherPlatform->publisherID)));
			$platform = new Platform(new NamedArguments(array('primaryKey' => $publisherPlatform->platformID)));
			$nameDisplay = $publisher->name . " / " . $platform->name;

			$statsArray = $publisherPlatform->getMonthlyOutliers($archiveInd, $year, $month);
		}else{
			$platform = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
			$nameDisplay = $platform->name;

			$statsArray = $platform->getMonthlyOutliers($archiveInd, $year, $month);
		}

		$totalRows = count($statsArray);

		?>

		<div id='div_outlierForm'>
		<table class="thickboxTable" style="background-image:url('images/tbtitle.gif');width:410px;">
		<tr>
		<td><span class='headerText'><?php echo $nameDisplay; ?></span><br /></td>
		</tr>
		<tr>

		<table class='dataTable' style='width:408px;margin-left:2px;'>

		<?php

			if ($totalRows == 0){
				echo "<tr><td>None currently</td></tr>";
			}else{
				foreach($statsArray as $monthlyStat){
					echo "<tr>";
					echo "<td style='width:170px;'>" . $monthlyStat['Title']. "<span id='span_error_overrideUsageCount_" . $monthlyStat['monthlyUsageSummaryID'] . "' style='color:red;'></span></td>";
					echo "<td style='width:50px;text-align:center;background-color:" . $monthlyStat['color'] . "'>" . $monthlyStat['usageCount'] . "</td>";
					echo "<td style='width:55px;'><input type='text' name = 'overrideUsageCount_" . $monthlyStat['monthlyUsageSummaryID'] . "' id = 'overrideUsageCount_" . $monthlyStat['monthlyUsageSummaryID'] . "' value='" . $monthlyStat['overrideUsageCount'] . "' style='width:50px'></td>";
					echo "<td style='width:80px;'><a href=\"javascript:updateOverride('" . $monthlyStat['monthlyUsageSummaryID'] . "');\" style='font-size:100%;'>update override</a><br /><a href=\"javascript:ignoreOutlier('" . $monthlyStat['monthlyUsageSummaryID'] . "');\" style='font-size:100%;'>ignore outlier</a></td>";
					echo "</tr>";
				}
			}

		?>

		</table>
		</td>
		</tr>
		<tr><td style='text-align:center;width:100%;'><br /><br /><a href='#' onclick='window.parent.updateFullStatsDetails(); window.parent.tb_remove(); return false'>Close</a></td></tr>
		</table>
		<input type="hidden" id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		<input type="hidden" id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>
		<input type="hidden" id='archiveInd' name='archiveInd' value='<?php echo $archiveInd; ?>'>
		<input type="hidden" id='year' name='year' value='<?php echo $year; ?>'>
		<input type="hidden" id='month' name='month' value='<?php echo $month; ?>'>

		<script type="text/javascript" src="js/forms/outlierSubmitForm.js?random=<?php echo rand(); ?>"></script>
		</div>


		<?php

		break;





	case 'getYearlyOverrideForm':
		if (isset($_GET['platformID'])) $platformID = $_GET['platformID']; else $platformID = '';
 		if (isset($_GET['publisherPlatformID'])) $publisherPlatformID = $_GET['publisherPlatformID'];

		$archiveInd = $_GET['archiveInd'];
		$year = $_GET['year'];


		$statsArray = array();
		if ($publisherPlatformID) {
			$publisherPlatform = new PublisherPlatform(new NamedArguments(array('primaryKey' => $publisherPlatformID)));
			$publisher = new Publisher(new NamedArguments(array('primaryKey' => $publisherPlatform->publisherID)));
			$platform = new Platform(new NamedArguments(array('primaryKey' => $publisherPlatform->platformID)));
			$nameDisplay = $publisher->name . " / " . $platform->name;

			$statsArray = $publisherPlatform->getYearlyOverrides($archiveInd, $year);
		}else{
			$platform = new Platform(new NamedArguments(array('primaryKey' => $platformID)));
			$nameDisplay = $platform->name;

			$statsArray = $platform->getYearlyOverrides($archiveInd, $year);
		}

		$totalRows = count($statsArray);


		?>

		<div id='div_overrideForm'>
		<table class="thickboxTable" style="background-image:url('images/tbtitle.gif');width:410px;">
		<tr>
		<td><span class='headerText'><?php echo $nameDisplay; ?></span><br />(showing only titles for which there were outliers during the year) </td>
		</tr>
		<tr>

		<table class='dataTable' style='width:408px;margin-left:2px;'>

		<?php

			if ($totalRows == 0){
				echo "<tr><td>None currently</td></tr>";
			}else{
				foreach($statsArray as $yearlyStat){
				?>
					<tr>
					<td width="149" class='alt'><?php echo $yearlyStat['Title']; ?></td>
					<td width="40" class='alt'>Total</td>
					<td width="40" class='alt'><?php echo $yearlyStat['totalCount']; ?></td>
					<td width="40" class='alt'><input name="overrideTotalCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" id="overrideTotalCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" type="text"value="<?php echo $yearlyStat['overrideTotalCount']; ?>" size="6" maxlength="6"/></td>
					<td width="40" class='alt'><a href="javascript:updateYTDOverride('<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>', 'overrideTotalCount')">update</a></td>
					</tr>
					<tr>
					<td width="149"><span id="span_error_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>_response" style='color:red;'></span></td>
					<td width="40">PDF</td>
					<td width="40"><?php echo $yearlyStat['ytdPDFCount']; ?></td>
					<td width="40"><input name="overridePDFCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" id="overridePDFCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" type="text"value="<?php echo $yearlyStat['overridePDFCount']; ?>" size="6" maxlength="6"/></td>
					<td width="40"><a href="javascript:updateYTDOverride('<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>', 'overridePDFCount')">update</a></td>
					</tr>
					<tr>
					<td width="149">&nbsp;</td>
					<td width="40">HTML</td>
					<td width="40"><?php echo $yearlyStat['ytdHTMLCount']; ?></td>
					<td width="40"><input name="overrideHTMLCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" id="overrideHTMLCount_<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>" type="text"value="<?php echo $yearlyStat['overrideHTMLCount']; ?>" size="6" maxlength="6"/></td>
					<td width="40"><a href="javascript:updateYTDOverride('<?php echo $yearlyStat['yearlyUsageSummaryID']; ?>', 'overrideHTMLCount')">update</a></td>
					</tr>
				<?php

				}
			}

		?>

		</table>
		</td>
		</tr>
		<tr><td style='text-align:center;width:100%;'><br /><br /><a href='#' onclick='window.parent.tb_remove(); return false'>Close</a></td></tr>
		</table>
		<input type="hidden" id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		<input type="hidden" id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>
		<input type="hidden" id='archiveInd' name='archiveInd' value='<?php echo $archiveInd; ?>'>
		<input type="hidden" id='year' name='year' value='<?php echo $year; ?>'>

		<script type="text/javascript" src="js/forms/overrideSubmitForm.js?random=<?php echo rand(); ?>"></script>
		</div>


		<?php

		break;






	//Add ISSNs
	case 'getAddISSNForm':
		if (isset($_GET['platformID'])) $platformID = $_GET['platformID']; else $platformID = '';
 		if (isset($_GET['publisherPlatformID'])) $publisherPlatformID = $_GET['publisherPlatformID'];
 		if (isset($_GET['titleID'])) $titleID = $_GET['titleID'];


		?>
		<div id='div_addISSNForm'>
		<table class="thickboxTable" style="width:200px;padding:2px;">
		<tr><td colspan='2'><span class='headerText'>Add ISSN</span><br /><br /></td></tr>
		<tr><td style='vertical-align:top;text-align:right;'><label for='issnType'><b>ISSN Type</b></label</td>
			<td>
			<select id='issnType' name='issnType' style='width:90px;'>
			<option value='alt'>alt</option>
			<option value='print'>print</option>
			<option value='online'>online</option>
			</select>
			</td>
		</tr>
		<tr><td style='vertical-align:top;text-align:right;'><label for='issn'><b>ISSN</b></label</td><td><input type='text' id='issn' name='issn' value="" style='width:90px;' /><span id='span_error_ISSN' style='color:red'></span></td></tr>


		<tr style="vertical-align:middle;">
			<td style="padding-top:8px;text-align:right;">&nbsp;</td>
			<td style="padding-top:8px;padding-right:8px;">
				<table class='noBorderTable' style='width:100%;'>
					<tr>
						<td style='text-align:left'><input type='button' value='submit' name='submitISSNForm' id ='submitISSNForm'></td>
						<td style='text-align:right'><input type='button' value='cancel' onclick="tb_remove()"></td>
					</tr>
				</table>
			</td>
		</tr>

		</table>

		</div>

		<input type="hidden" id='titleID' name='titleID' value='<?php echo $titleID; ?>'>
		<input type="hidden" id='platformID' name='platformID' value='<?php echo $platformID; ?>'>
		<input type="hidden" id='publisherPlatformID' name='publisherPlatformID' value='<?php echo $publisherPlatformID; ?>'>

		<script type="text/javascript" src="js/forms/issnSubmitForm.js?random=<?php echo rand(); ?>"></script>


		<?php

		break;




	//Related Titles (this form is display only)
	case 'getRelatedTitlesForm':
 		if (isset($_GET['titleID'])) $titleID = $_GET['titleID'];

		$title = new Title(new NamedArguments(array('primaryKey' => $titleID)));

		?>
		<div id='div_relatedTitles'>
		<table class="thickboxTable" style="width:250px;padding:2px;">
		<tr><td><span class='headerText'>Associated Titles and ISSNs</span><br /></td></tr>
		<tr>
		<td>

		<table border="0" style="width:246px">
		<?php



			$relatedTitle = new Title();
			foreach($title->getRelatedTitles as $relatedTitle) {

				echo "<tr>";
				echo "<td colspan = '2' style='width:250px'><b>" . $relatedTitle->title . "</b></td>";
				echo "</tr>";

				foreach($relatedTitle->getISSNs as $relatedTitleISSN) {
					$displayISSN = substr($relatedTitleISSN->issn,0,4) . "-" . substr($relatedTitleISSN->issn,4,4);

					echo "<tr>";
					echo "<td style='width:40px'>" . $relatedTitleISSN->issnType . "</td>";
					echo "<td style='width:210px'>" . $displayISSN . "</td>";
					echo "</tr>";

				}


			}
		?>
		</table>

		</td>
		</tr>

		<tr>
		<td style='text-align:center;width:100%;'><br /><br /><a href='#' onclick='window.parent.tb_remove(); return false'>Close</a>
		</td>
		</tr>

		</table>

		</div>


		<?php

		break;








	//user form on the admin tab needs its own form since there are other attributes
	case 'getAdminUserUpdateForm':
		if (isset($_GET['loginID'])) $loginID = $_GET['loginID']; else $loginID = '';

		if ($loginID != ''){
			$update='Update';
			$updateUser = new User(new NamedArguments(array('primaryKey' => $loginID)));
		}else{
			$update='Add New';
		}


		?>
		<div id='div_updateForm'>
		<table class="thickboxTable" style="width:245px;padding:2px;">
		<tr><td colspan='3'><span class='headerText'><?php echo $update; ?> User</span><br /><br /></td></tr>
		<tr><td colspan='2' style='width:135px;'><label for='loginID'><b>Login ID</b></label</td><td><input type='text' id='loginID' name='loginID' value='<?php echo $loginID; ?>' style='width:140px;' /></td></tr>
		<tr><td colspan='2'><label for='firstName'><b>First Name</b></label</td><td><input type='text' id='firstName' name='firstName' value="<?php if (isset($updateUser)) echo $updateUser->firstName; ?>" style='width:140px;' /></td></tr>
		<tr><td colspan='2'><label for='lastName'><b>Last Name</b></label</td><td><input type='text' id='lastName' name='lastName' value="<?php if (isset($updateUser)) echo $updateUser->lastName; ?>" style='width:140px;' /></td></tr>
		<tr><td><label for='privilegeID'><b>Privilege</b></label</td>
		<td>
				<fieldset id="foottip">
				<a href="#footnote_priv"><img src='images/help.gif'></a>
				<div id="footnote_priv" style='display:none;'>Add/Edit users have access to everything<br />except the Admin tab and admin users<br />have access to everything</div>
				</fieldset>

		</td>
		<td>
		<select name='privilegeID' id='privilegeID' style='width:145px'>
		<?php



		$display = array();
		$privilege = new Privilege();

		foreach($privilege->allAsArray() as $display) {
			if ($updateUser->privilegeID == $display['privilegeID']){
				echo "<option value='" . $display['privilegeID'] . "' selected>" . $display['shortName'] . "</option>";
			}else{
				echo "<option value='" . $display['privilegeID'] . "'>" . $display['shortName'] . "</option>";
			}
		}

		?>
		</select>
		</td>
		</tr>
		<tr style="vertical-align:middle;">
		<td colspan='2' style="padding-top:8px;text-align:right;">&nbsp;</td>
		<td style="padding-top:18px;padding-right:8px;text-align:left;"><input type='button' value='<?php echo $update; ?>' onclick='javascript:window.parent.submitUserData("<?php echo $loginID; ?>");'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='button' value='cancel' onclick="window.parent.tb_remove(); return false"></td>
		</tr>

		</table>

		</div>


		<script type="text/javascript" src="js/forms/adminUserForm.js?random=<?php echo rand(); ?>"></script>
		<?php

		break;






	default:
       echo "Function " . $_REQUEST['function'] . " not set up!";
       break;


}



?>