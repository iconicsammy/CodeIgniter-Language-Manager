<?php defined('BASEPATH') OR exit('No direct script access allowed');?>

<?php
//what do we want to see?
if($view=='installedlanguages'){
//currently installed languages. Show if we have?
if(!empty($languages)){
//we have languages. Load now
?>
<select id="languages" name="languages">
<?php
foreach($languages as $langname){
?>
<option value="<?php echo $langname;?>"><?php echo $langname;?></option>
<?php
}
?>
</select> 

<select id="modules"></select>

<div id="mylangactionbuttons">
<p><input type="text" id="langname" /></p>

 <input type="button" id="replicatelang" value="<?php echo $this->lang->line('mylang_replicatelanguage');?>" /> <input type="button" id="renamelang" value="<?php echo $this->lang->line('mylang_renamelanguage');?>" /> <input type="button" id="btnviewmodules" value="<?php echo $this->lang->line('mylang_viewmodules');?>" /> <input type="button" id="renamemodule" value="<?php echo $this->lang->line('mylang_renamemodule');?>" /> <input type="button" value="<?php echo $this->lang->line('mylang_newlanguagemodule');?>" id="btnnewmodule" /> <input type="button" id="btndel" value="<?php echo $this->lang->line('mylang_dellanguage');?>" /> <input type="button" id="btneditmodule" value="<?php echo $this->lang->line('mylang_editmodule');?>" /> <input type="button" id="btndelmod" value="<?php echo $this->lang->line('mylang_delmodule');?>" />
</div>

<p id="mylangstatus"><?php echo $this->lang->line('mylang_tip');?></p>

<div id="langblock">

<form action="#" id="mylangform" method="post">
<table id='mlblock'>
<thead>
<tr><th>Name</th><th>Value</th><th>&nbsp;</th></tr>
</thead>
<tbody id="contents"></tbody>
<tfoot><tr colspan="3"><td><input type="button" value="<?php echo $this->lang->line('mylang_savemodule');?>" id="mylangsavemodule" class="mylanghidden" /></td></tr></tfoot>
</table>
</form>
</div>



<?php
}


}


?>




<script>

$(document).ready(function() {

$("#languages").change(function () {
//language change.
cleanLangLines();
});

$("#modules").change(function () {
//module change.
cleanLangLines();
});

//delete module:
 // do stuff when DOM is ready
  $("#btndelmod").click(function() {
 hideSaveButton();
 cleanLangLines();
var sourceLang=selectedLanguage();
      if (sourceLang==false) {
    displayStatus('<?php echo $this->lang->line("mylang_err_selectlangfirst");?>');
	return false;
  }
//module name
var moduleName=selectedModule();
if (moduleName==false) {
    displayStatus('<?php echo $this->lang->line("mylang_err_selectmodtodel");?>');
	return false;
  }

var postForm = { //Fetch form data
            'lang':sourceLang, //Store name fields value
			'mod':moduleName,
			'<?php echo $this->security->get_csrf_token_name();?>': '<?php echo $this->security->get_csrf_hash();?>'
        };

       $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>mylang/delmodule",
                data: postForm,
                dataType : "text",
                cache: "false",
				
                success: function (result) {
                    //remove it
				
					if(result=='1'){
					$("#modules option:selected").remove();
					}
					
					else {
					displayStatus('<?php echo $this->lang->line("mylang_err_modnodelete");?>');
					}
					
                },
				
				fail: function (result){
			
				displayStatus('<?php echo $this->lang->line("mylang_err_tech");?>');
				}
				
				
            });
			
			
});

//----------------------------

  // do stuff when DOM is ready
  $("#btndel").click(function() {
 hideSaveButton();
 cleanLangLines();
var lang=selectedLanguage();
      if (lang==false) {
    displayStatus('<?php echo $this->lang->line("mylang_err_selectlangtodel");?>');
	return false;
  }

var postForm = { //Fetch form data
            'lang':lang, //Store name fields value
			'<?php echo $this->security->get_csrf_token_name();?>': '<?php echo $this->security->get_csrf_hash();?>'
        };

       $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>mylang/dellang",
                //url: baseurl + 'sms/get_dept_employee',
                data: postForm,
                dataType : "text",
                cache: "false",
                success: function (result) {
                    //remove it
				
					if(result=='1'){
					$("#languages option:selected").remove();
					$('#modules').empty();
					}
					else {
					displayStatus('<?php echo $this->lang->line("mylang_err_langnodelete");?>');
					}
                },
				fail: function (result){
			
				displayStatus('<?php echo $this->lang->line("mylang_err_tech");?>');
				}
				
				
            });
			
});

//------------------------------ADD NEW MODULE-----------------------
 $("#btnnewmodule").click(function() {
 hideSaveButton();
 cleanLangLines();
var lang=selectedLanguage();
      if (lang==false) {
    displayStatus('<?php echo $this->lang->line("mylang_err_selectlangtoaddmodule");?>');
	return false;
  }
  
  var moduleName=$('#langname').val().trim();
  if(moduleName==''){
  displayStatus('<?php echo $this->lang->line("mylang_err_enternewmodulename");?>');
  $('#langname').focus();
	return false;
  }
  
  //check if it exists already or not?
 if( moduleExists(moduleName)==false){
  displayStatus('<?php echo $this->lang->line("mylang_err_moduleexistsalready");?>');
  $('#langname').focus();
	return false;
 
 }
 

var postForm = { //Fetch form data
            'lang':lang, //Store name fields value
			'module':moduleName, //Store name fields value
			'<?php echo $this->security->get_csrf_token_name();?>': '<?php echo $this->security->get_csrf_hash();?>'
        };

       $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>mylang/addmodule",
                //url: baseurl + 'sms/get_dept_employee',
                data: postForm,
                dataType : "text",
                cache: "false",
                success: function (result) {
                     if(result=='1'){
					 //add to modules now
					 $('#modules').append($("<option/>", {
        value: moduleName,
        text: moduleName
    }));
	$('#modules option[value="'+moduleName+'"]').attr('selected', 'selected');
	
	
					 }
					 else {
					 displayStatus('<?php echo $this->lang->line("mylang_err_modulenoadd");?>');
					 }
              },
				fail: function (result){
			
				displayStatus('<?php echo $this->lang->line("mylang_err_tech");?>');
				}
				
				
            });
			
});	
//------------------------------VIEW MODULES--------------------------
 $("#btnviewmodules").click(function() {
  hideSaveButton();
  cleanLangLines();
 $('#modules').empty();
var lang=selectedLanguage();
      if (lang==false) {
    displayStatus('<?php echo $this->lang->line("mylang_err_selectlangtoviewmodule");?>');
	return false;
  }

var postForm = { //Fetch form data
            'lang':lang, //Store name fields value
			'<?php echo $this->security->get_csrf_token_name();?>': '<?php echo $this->security->get_csrf_hash();?>'
        };

       $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>mylang/viewmodules",
                //url: baseurl + 'sms/get_dept_employee',
                data: postForm,
                dataType : "json",
                cache: "false",
                success: function (result) {
                    //remove it
					var resultLen=result.length;
					if(resultLen>0){
					for (var i = 0; i < resultLen; i++) {
					 $('#modules').append($("<option/>", {
                     value: result[i],
                     text: result[i]
                     }));
   
                  }
					}
              },
				fail: function (result){
			
				displayStatus('<?php echo $this->lang->line("mylang_err_tech");?>');
				}
				
				
            });
			
});	 
//---------------------------------------------------------------------
//---------------------------REPLICATE LANGUAGE-----------------------
  $("#replicatelang").click(function() {
 hideSaveButton();

      if (selectedLanguage()==false) {
      displayStatus('<?php echo $this->lang->line("mylang_err_selectlangtoreplicate");?>');
	  return false;
      }
  
  //name:
  var langName=$('#langname').val().trim();
  if(langName==''){
  displayStatus('<?php echo $this->lang->line("mylang_err_enternewlanguagename");?>');
  $('#langname').focus();
	return false;
  }
  
  //check if it exists already or not?
 if( languageExists(langName)==false){
  displayStatus('<?php echo $this->lang->line("mylang_err_langexistsalready");?>');
  $('#langname').focus();
	return false;
 
 }
  


var postForm = { //Fetch form data
            'lang':$("#languages option:selected").val(), //Store name fields value
			'newlang':langName,
			'<?php echo $this->security->get_csrf_token_name();?>': '<?php echo $this->security->get_csrf_hash();?>'
        };

       $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>mylang/replicate",
                //url: baseurl + 'sms/get_dept_employee',
                data: postForm,
                dataType : "text",
                cache: "false",
                success: function (result) {
				
                    //remove it
					if(result=='1'){
					displayStatus('<?php echo $this->lang->line("mylang_ok_langduplicated");?>');
					//add to list of languages
					$('#languages').append($("<option/>", {
        value: langName,
        text: langName
    }));
	$('#languages option[value="'+langName+'"]').attr('selected', 'selected');
	$('#modules').empty();
					}
					else if(result=='0') {
					displayStatus('<?php echo $this->lang->line("mylang_err_langnoreplicated");?>');
					}
					else {
					displayStatus(result);
					}
                },
				fail: function (result){
				displayStatus('<?php echo $this->lang->line("mylang_err_tech");?>');
				}
				
				
            });
			
});

//--------------------------------------------------------------
  $("#renamelang").click(function() {

 //rename
var sourceLang=selectedLanguage();
      if (sourceLang==false) {
    displayStatus('<?php echo $this->lang->line("mylang_err_selectlangtorename");?>');
	return false;
  }
  
  //name:
  var langName=$('#langname').val().trim();
  if(langName==''){
  displayStatus('<?php echo $this->lang->line("mylang_err_enternewlanguagename");?>');
  $('#langname').focus();
	return false;
  }
  
  //check if it exists already or not?
 if( languageExists(langName)==false){
  displayStatus('<?php echo $this->lang->line("mylang_err_langexistsalready");?>');
  $('#langname').focus();
	return false;
 
 }
var postForm = { //Fetch form data
            'lang':sourceLang, //Store name fields value
			'newname':langName,
			'<?php echo $this->security->get_csrf_token_name();?>': '<?php echo $this->security->get_csrf_hash();?>'
        };

       $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>mylang/changename",
                //url: baseurl + 'sms/get_dept_employee',
                data: postForm,
                dataType : "text",
                cache: "false",
                success: function (result) {
			
                    //remove it
					if(result=='1'){
					displayStatus('<?php echo $this->lang->line("mylang_ok_langrenamed");?>');
					//rename it now
					$('#languages option:contains("'+sourceLang+'")').text(langName);
	                $('#languages option:contains("'+langName+'")').val(langName);
	
					}
					else if(result=='0') {
					displayStatus('<?php echo $this->lang->line("mylang_err_langnorename");?>');
					}
					else {
					displayStatus(result);
					}
                },
				fail: function (result){
				displayStatus('<?php echo $this->lang->line("mylang_err_tech");?>');
				}
				
				
            });
			
});

//---------------------------------------
  $("#renamemodule").click(function() {

 //rename
var sourceLang=selectedLanguage();
      if (sourceLang==false) {
    displayStatus('<?php echo $this->lang->line("mylang_err_selectlangfirst");?>');
	return false;
  }
//module name
var moduleName=selectedModule();
if (moduleName==false) {
    displayStatus('<?php echo $this->lang->line("mylang_err_selectmoduletorename");?>');
	return false;
  }
  //name:
  var newName=$('#langname').val().trim();
  if(newName==''){
  displayStatus('<?php echo $this->lang->line("mylang_err_enternewmodulename");?>');
  $('#langname').focus();
	return false;
  }
  
  //check if it exists already or not?
 if( moduleExists(newName)==false){
  displayStatus('<?php echo $this->lang->line("mylang_err_modexistsalready");?>');
  $('#langname').focus();
	return false;
 
 }
 
var postForm = { //Fetch form data
            'lang':sourceLang, //Store name fields value
			'modname':moduleName,
			'newname':newName,
			'<?php echo $this->security->get_csrf_token_name();?>': '<?php echo $this->security->get_csrf_hash();?>'
        };

       $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>mylang/renamemodule",
                //url: baseurl + 'sms/get_dept_employee',
                data: postForm,
                dataType : "text",
                cache: "false",
                success: function (result) {
			
                    //remove it
					if(result=='1'){
					displayStatus('<?php echo $this->lang->line("mylang_ok_modulerenamed");?>');
					//rename it now
					$('#modules option:contains("'+moduleName+'")').text(newName);
	                $('#modules option:contains("'+newName+'")').val(newName);
	
					}
					else if(result=='0') {
					displayStatus('<?php echo $this->lang->line("mylang_err_modnorename");?>');
					}
					else {
					displayStatus(result);
					}
                },
				fail: function (result){
				displayStatus('<?php echo $this->lang->line("mylang_err_tech");?>');
				}
				
				
            });
			
});

//--------------------------------------------

  $("#btneditmodule").click(function() {
$('#contents').html('');
 //rename
var sourceLang=selectedLanguage();
      if (sourceLang==false) {
    displayStatus('<?php echo $this->lang->line("mylang_err_selectlangfirst");?>');
	return false;
  }
//module name
var moduleName=selectedModule();
if (moduleName==false) {
    displayStatus('<?php echo $this->lang->line("mylang_err_selectmoduletoedit");?>');
	return false;
  }
  
 
var postForm = { //Fetch form data
            'lang':sourceLang, //Store name fields value
			'modname':moduleName,
			'<?php echo $this->security->get_csrf_token_name();?>': '<?php echo $this->security->get_csrf_hash();?>'
        };

       $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>mylang/loadmodule",
                //url: baseurl + 'sms/get_dept_employee',
                data: postForm,
                dataType : "json",
                cache: "false",
                success: function (data) {
				var dataLen=data.length;
				var i=1;
		             if (dataLen>0){
					$.each(data, function() {
  $.each(this, function(key, value) {
    /// do stuff
	
    $('#contents').append('<tr id="'+i+'"><td><input type="text" value="'+key+'" id="key'+i+'" name="key'+i+'" class="mylangkey"  maxlength="100"/></td><td><textarea class="mylangvalue" name="val'+i+'" id="val'+i+'">'+value+'</textarea></td><td><span class="mylanghidden" id="bkey'+i+'">'+key+'</span><span class="mylanghidden" id="bval'+i+'">'+value+'</span> <a href="#" class="deleteline"></a> <a href="#" class="originalline"></a></td></tr>');
	i++;
  });
  
});
// Now the two will work



}
			$('#contents').append('<tr id="newrow"><td><input type="text" value="" id="newkey" class="mylangkey_add"/></td><td><textarea id="newval" value="" class="mylangvalue_add"></textarea></td><td><input type="button" id="mylangaddline" /></td></tr>');
//attach click event
attachActionButtons();
showSaveButton();		
                },
				fail: function (result){
				displayStatus('<?php echo $this->lang->line("mylang_err_tech");?>');
				}
				
				
            });
			
});

//-------------------------------------------------
  function saveModule(){

//save the module
var sourceLang=selectedLanguage();
   
//module name
var moduleName=selectedModule();

  
  if(sourceLang==false || moduleName==false){
  displayStatus('<?php echo $this->lang->line("mylang_err_savemissinglangmodule");?>');
	return false;
  }
  

  //validate fields here: ALL MUST BE GIVEN
 var paths="";
 var row=1;
 var txtValue='';
 var id="";
  $('#mylangform input[type="text"],textarea').each(function() {
  
  txtValue=$(this).val().trim();
  if(txtValue==''){
  id=$(this).attr('id');

  if(id!='newkey' && id!='newval'){
  alert("<?php echo $this->lang->line('mylang_err_savemissingfields');?>");
		return false;
		}
  }
  
      if(row==1){
	  paths=paths+txtValue+'=';
	  row=2;
	  }
	  else{
	  paths=paths+txtValue+'&';
	  row=1;
	  }
   
        
  });
  var mistirName="<?php echo $this->security->get_csrf_token_name();?>";
  var mistirValue="<?php echo $this->security->get_csrf_hash();?>";
 paths=paths+'lang='+sourceLang+'&modname='+moduleName+'&'+mistirName+'='+mistirValue;

       $.ajax({
                type: "POST",
                url: "<?php echo base_url()?>mylang/savemodule",
                //url: baseurl + 'sms/get_dept_employee',
                data: paths,
                dataType : "text",
                cache: "false",
                success: function (data) {
				
				 if(data==1){
				 displayStatus('<?php echo $this->lang->line("mylang_ok_modsaved");?>');
				 }
				 else{
				 displayStatus('<?php echo $this->lang->line("mylang_err_modnosaved");?>');
				 }
		             
                },
				fail: function (result){
				displayStatus('<?php echo $this->lang->line("mylang_err_tech");?>');
				}
				
				
            });
			
			 
			
}
//--------------------------------------------------

function showSaveButton(){
$('#mylangsavemodule').removeClass('mylanghidden');
$('#mylangsavemodule').addClass('mylangshow');
$("#mylangsavemodule").click(function(e) {
 e.preventDefault();
saveModule();
});

}

function hideSaveButton(){
$('#mylangsavemodule').removeClass('mylangshow');
$('#mylangsavemodule').addClass('mylanghidden');
}

function addLine(){
//add now
var key=$('#newkey').val().trim();

var valOfKey=$('#newval').val().trim();
if(key=='' || valOfKey==''){
displayStatus('<?php echo $this->lang->line("mylang_err_addkeyvalue");?>');return false;
}
//remove the row now
$('#newrow').remove();
//add values
var i=$('#contents').find('tr').length+1;
$('#contents').append('<tr id="'+i+'"><td><input type="text" value="'+key+'" id="key'+i+'" name="key'+i+'" class="mylangkey"  maxlength="100"/></td><td><textarea class="mylangvalue" name="val'+i+'" id="val'+i+'">'+valOfKey+'</textarea></td><td><span class="mylanghidden" id="bkey'+i+'">'+key+'</span><span class="mylanghidden" id="bval'+i+'">'+valOfKey+'</span> <a href="#" class="deleteline"></a> <a href="#" class="originalline"></a></td></tr>');

$('#contents').append('<tr id="newrow"><td><input type="text" value="" id="newkey" class="mylangkey_add"/></td><td><textarea id="newval" value="" class="mylangvalue_add"></textarea></td><td><input type="button" id="mylangaddline"/></td></tr>');

attachActionButtons();
showSaveButton();
}

function attachActionButtons(){

$(".originalline").click(function(e) {
 e.preventDefault();
resetOriginal(this);
});

$(".deleteline").click(function(e) {
 e.preventDefault();
deleteLine(this);
});

$("#mylangaddline").click(function(e) {
 e.preventDefault();
addLine();
});

}
//-------------------------------------------------------------------
function cleanLangLines(){
$('#contents').html('');
}

function resetOriginal(caller){
//reset the key/pair value to orginal.

var id=$(caller).closest('tr').attr('id'); //this is the row it belongs
//back up text is in bkeyid,bvalid and boxes are keyid,valid
$('#key'+id).val($('#bkey'+id).html());
$('#val'+id).val($('#bval'+id).html());
}


function deleteLine(caller){
//delete the line

$(caller).closest('tr').remove();

}



});




function displayStatus(text){
$('#mylangstatus').html(text);
$('#mylangstatus').fadeIn(1000);
}

function languageExists(lang){
if( $("#languages option[value='"+lang+"']").length > 0){
return false;
 
 }
 return true;

}

function moduleExists(module){
if( $("#modules option[value='"+module+"']").length > 0){
return false;
 
 }
 return true;

}

function selectedLanguage(){
//return selected language. else false
if (!$("#languages option:selected").length) {
    
	return false;
  }
return $("#languages option:selected").val();
} 

function selectedModule(){
//return selected module. else false
if (!$("#modules option:selected").length) {
    
	return false;
  }
return $("#modules option:selected").val();
} 
	

</script>