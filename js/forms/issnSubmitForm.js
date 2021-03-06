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
*/

 $(function(){


	 $("#submitISSNForm").click(function () {
	 	submitISSN();
	 });
	 

	//do submit if enter is hit
	$('#ISSN').keyup(function(e) {
	      if(e.keyCode == 13) {
		submitISSN();
	      }
	}); 
	
	  	 
 });
 





function submitISSN(){

  
  if (validateForm() === true) {
	  $('#span_' + $("#titleID").val() + '_feedback').html('');
	  
	  $.ajax({
		 type:       "POST",
		 url:        "ajax_processing.php?action=addISSN",
		 cache:      false,
		 data:       { issn: $("#issn").val(), issnType: $("#issnType").val(), titleID: $("#titleID").val() },
		 success:    function(html) {
			window.parent.tb_remove();
			window.parent.updateTitleDetails($("#titleID").val());
			return false;		
		 }


	 });
	 
   }

}



//validates fields
function validateForm (){
	myReturn=0;
	if (!isISSN($("#issn").val())){
		$('#span_error_ISSN').html('<br />ISSN must be valid format.');
		myReturn=1;
	}
	
	
	if (myReturn == "1"){
		return false;
	}else{
		return true;
	}
}
