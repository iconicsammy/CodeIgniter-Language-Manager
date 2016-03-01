<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/*
@plugin		Language Editor Plugin for CodeIgniter 3.0
@subpackage	Controller/View
@author		K Sium
@email     kmsium@gmail.com		
  */
class Mylang extends CI_Controller {

	/**
	 * Mylang controller.
	 * Aim: a controller for the language editor
	 * 
	 *
	 * Maps to the following URL
	 * 		http://www.businessname.biz/mylangs
	 * 
	 */
	
	 public function __construct()
        {
                 parent::__construct();
                // Your own constructor cod
				 
				$this->lang->load('mylang','english');
				
        }
	public function index()
	{
	//load avaliable languages here: main folders.
	
	
	$languagesFolder=APPPATH."language\\";
	$installedLanguages=array();

$dirs = array_filter(glob($languagesFolder.'*'), 'is_dir'); 
if(!empty($dirs)){
foreach($dirs as $dir){
$path=explode("\\",$dir);
$installedLanguages[]= end($path);
}
}
$data['view']='installedlanguages';
$data['languages']=$installedLanguages;
$this->load->view('header');

$this->load->view('mylangv',$data);
	//$this->load->view('home',$data);
	}
	
//--------------------------------------------	
	
function viewmodules(){
//view modules in the given language i.e. all _lang.php files in the selected language folder
if($this->input->post('lang')){

	 $path=$this->input->post('lang');
	 //delete the lang
	$path=APPPATH."language\\$path\\";
    $langFiles=$this->getlangfiles($path);
    
	$return=array();
	//do we have language files here?
	if(!empty($langFiles)){
	//remove the _lang.php part
	         foreach($langFiles as $fileInfo){
			 $langFile=explode("\\",$fileInfo);
			 $langFile=end($langFile);
			 //remove last _lang.php here
			 $langFile=substr($langFile,0,strlen($langFile)-9);
			 $return[]=$langFile;
			 }
	
	}
 	 
	 }
	 
	echo json_encode($return);



}
//-------------------------------------------------------------------
	
	function dellang(){
	
	//delete the selected language fully
     if($this->input->post('lang')){

	 $path=$this->input->post('lang');
	 //delete the lang
	$path=APPPATH."language\\$path";
	$this->deletedir($path);
	 if(!file_exists($path) && !is_dir($path)){
	 echo(1);
	 exit;
	 }
	 else {
	 exit(0);
	 exit;
	 }
	 
	 
	 }
	echo 0;
	exit;
	
	}
	
	///--------------------------------------------------------
	
		function replicate(){
	
	//replicate selected language.
     if($this->input->post('lang')){

	 $sourceLang=$this->input->post('lang');
	 $destLang=strtolower($this->input->post('newlang'));
	 
	 //both must be given
	 if(!$sourceLang || !$destLang){
	 echo $this->lang->line('mylang_err_replicamissinginfo');
	 exit;
	 }
	 
	 //Both given: Source must exist.

	$sourceLang=APPPATH."language\\$sourceLang";
	if(!file_exists($sourceLang) || !is_dir($sourceLang)){
	echo $this->lang->line('mylang_err_replicamissinginfo');
	 exit;
	}
	$destLang=APPPATH."language\\$destLang";
	//SourceLang Exists well. New language must be unique
	if(file_exists($destLang) && is_dir($destLang)){
	echo $this->lang->line('mylang_err_langexistsalready');
	 exit;
	}
	if(substr($sourceLang,strlen($sourceLang)-1)!='\\'){
	$sourceLang.='\\';
	}
	
	$destLang.='\\';
	
	
	//all seems well. get valid lang files now
	$files=$this->getlangfiles($sourceLang);
	
	 if(!empty($files)){
	 //we have files. Create the directory first
	 if(@mkdir($destLang)){
	 //copy the files nows
	
	   foreach($files as $file){
	  //get file name here
	  $fileInfo= pathinfo($file);
	   @copy($file,$destLang.'\\'.$fileInfo['filename'].'.php');	  
	   }
	 //Folder directory is made.
	 $copiedFiles=$this->getlangfiles($destLang);
	 
	 //compare copied and original files now. NB: ONLY quantity of files
	 if(count($files)==count($copiedFiles)){
	 //add index.html from root of codeigniter
	 @copy('index.html',$destLang.'index.html');
	 echo 1;
	 exit; 
	 }
	 else{
	 //remove the directory here	  
	$this->deletedir($destLang);
	 echo 0;
	 exit;
	 }
	 }
	 
echo 0;
	exit;
	
	 }

	
	}

	echo 0;
	exit;
	

	}
	
	//--------------------------
	function addmodule(){
	
	//add a module to selected language.
     if($this->input->post('lang')){

	 $sourceLang=$this->input->post('lang');
	 $newModule=strtolower($this->input->post('module'));
	 
	 //both must be given
	 if(!$sourceLang || !$newModule){
	 echo $this->lang->line('mylang_err_newmodulemissinginfo');
	 exit;
	 }
	 
	 //Both given: Source must exist.

	$sourceLang=APPPATH."language\\$sourceLang";
	if(!file_exists($sourceLang) || !is_dir($sourceLang)){
	echo $this->lang->line('mylang_err_newmodulemissinginfo');
	 exit;
	}
	if(substr($sourceLang,strlen($sourceLang)-1)!='\\'){
	$sourceLang.='\\';
	}
	$newModule=$sourceLang."$newModule"."_lang.php";
	//Module must be unique
	if(file_exists($newModule) && is_file($newModule)){
	echo $this->lang->line('mylang_err_moduleexistsalready');
	 exit;
	}
	
  // create the file now
  
$basePathInfo='<?php '."\n\n".' defined(\'BASEPATH\') OR exit(\'No direct script access allowed\');'."\n\n";
  $this->load->helper('file');

if ( ! write_file($newModule, $basePathInfo))
{
        echo 0;
		exit;
}
else
{
         echo 1;
		 exit;
}
	

	}

	echo 0;
	exit;
	

	}
	
//--------------------------------------------------------------------------------------------
function changename(){
	
	//change name
     if($this->input->post('lang')){

	 $sourceLang=$this->input->post('lang');
	 $newName=strtolower($this->input->post('newname'));
	 
	 //both must be given
	 if(!$sourceLang || !$newName){
	 echo $this->lang->line('mylang_err_renamemissinginfo');
	 exit;
	 }
	 
	 //Both given: Source must exist.

	$sourceLang=APPPATH."language\\$sourceLang";
	if(!file_exists($sourceLang) || !is_dir($sourceLang)){
	echo $this->lang->line('mylang_err_renamemissinginfo');
	 exit;
	}
	$destLang=APPPATH."language\\$newName";
	//SourceLang Exists well. New language must be unique
	if(file_exists($destLang) && is_dir($destLang)){
	echo $this->lang->line('mylang_err_langexistsalready');
	 exit;
	}
	
	//rename it here normally
	if(@rename($sourceLang,$destLang)){
	echo 1;
	exit;
	}
	else{
	echo 0;
	exit;
	}
	
	}

	echo 0;
	exit;
	

	}
//--------------------------------------------------------------------
function renamemodule(){
	
	//change name of module
     if($this->input->post('lang')){

	 $sourceLang=$this->input->post('lang');
	 $modName=strtolower($this->input->post('modname'));
	 $newName=strtolower($this->input->post('newname'));
	 
	 //both must be given
	 if(!$sourceLang || !$modName || !$newName){
	 echo $this->lang->line('mylang_err_renamemodulemissinginfo');
	 exit;
	 }
	 
	 //All given: Source must exist.
	 $sourceLang=APPPATH."language\\$sourceLang";
if(substr($sourceLang,strlen($sourceLang)-1)!='\\'){
	$sourceLang.='\\';
	}
	$modName=$sourceLang.$modName.'_lang.php';
	if(!file_exists($modName) || !is_file($modName)){
	echo $this->lang->line('mylang_err_renamemodulemissinginfo');
	 exit;
	}
	$newName=$sourceLang.$newName.'_lang.php';
	//Module must be unique
	if(file_exists($newName) && is_dir($newName)){
	echo $this->lang->line('mylang_err_modexistsalready');
	 exit;
	}
	
	//rename it here normally
	if(@rename($modName,$newName)){
	echo 1;
	exit;
	}
	else{
	echo 0;
	exit;
	}
	
	}

	echo 0;
	exit;
	

	}
//-------------------------------------------
function delmodule(){
	
	//delete af module
     if($this->input->post('lang')){

	 $sourceLang=$this->input->post('lang');
	 $modName=strtolower($this->input->post('mod'));
	 
	 
	 //both must be given
	 if(!$sourceLang || !$modName){
	 echo $this->lang->line('mylang_err_delmodulemissinginfo');
	 exit;
	 }
	 
	 //All given: Source must exist.
	 $sourceLang=APPPATH."language\\$sourceLang";
if(substr($sourceLang,strlen($sourceLang)-1)!='\\'){
	$sourceLang.='\\';
	}
	$modName=$sourceLang.$modName.'_lang.php';
	if(!file_exists($modName) || !is_file($modName)){
	echo $this->lang->line('mylang_err_delmodulemissinginfo');
	 exit;
	}
	
	//delete it here
	if(@unlink($modName)){
	echo 1;
	exit;
	}
	echo 0;
	exit;

	
	}

	echo 0;
	exit;
	

	}

//-----------------------------------------------

function loadmodule(){
	
	//load language file module
     if($this->input->post('lang')){

	 $sourceLang=$this->input->post('lang');
	 $modName=strtolower($this->input->post('modname'));
	
	 
	 //both must be given
	 if(!$sourceLang || !$modName){
	 echo $this->lang->line('mylang_err_loadmodulemissinginfo');
	 exit;
	 }
	 
	 //All given: Source must exist.
	 $sourceLang=APPPATH."language\\$sourceLang";
     if(substr($sourceLang,strlen($sourceLang)-1)!='\\'){
  	$sourceLang.='\\';
	 }
	
	$modName=$sourceLang.$modName.'_lang.php';
	if(!file_exists($modName) || !is_file($modName)){
	 echo $this->lang->line('mylang_err_loadmodulemissinginfo');
	 exit;
	}
	

	
	$return=array();
	
	require($modName);
	if(!empty($lang)){
	foreach($lang as $key=>$value){
	
	$return[]=array($key => $value);
	
	}
	
	}
	
	echo json_encode($return);
	exit;
	
	}

	echo 0;
	exit;
	

	}

//--------------------------------SUPPORT------------------------
function savemodule(){
	
	//save language file module
     if($this->input->post('lang')){

	 $sourceLang=$this->input->post('lang');
	 $modName=strtolower($this->input->post('modname'));
	
	 
	 //both must be given
	 if(!$sourceLang || !$modName){
	 echo $this->lang->line('mylang_err_loadmodulemissinginfo');
	 exit;
	 }
	 
	 //All given: Source must exist.
	 $sourceLang=APPPATH."language\\$sourceLang";
     if(substr($sourceLang,strlen($sourceLang)-1)!='\\'){
  	$sourceLang.='\\';
	 }
	
	$modName=$sourceLang.$modName.'_lang.php';
	if(!file_exists($modName) || !is_file($modName)){
	 echo $this->lang->line('mylang_err_loadmodulemissinginfo');
	 exit;
	}
	

$ignoreArgs=array(strtolower($this->security->get_csrf_token_name()),'lang','modname');
$content='<?php '."\n\n".' defined(\'BASEPATH\') OR exit(\'No direct script access allowed\');'."\n\n";
$langKey='';
$langVal='';
$_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
foreach($_POST as $key=>$value){

$key=strtolower($key);
$value=strtolower($value);
//both must be given
if($key && $value){
if (!in_array($key,$ignoreArgs)){

$value=addslashes($value);
$key=addslashes($key);
$content.='$lang[\''.$key.'\']=\''.$value.'\';'."\n";
}

}

}

$this->load->helper('file');
if ( ! write_file($modName, $content))
{
        echo 0;
		exit;
}
else
{
         echo 1;
		 exit;
}
	
	}

	echo 0;
	exit;
	

	}

//-----------------------PRIVATE SUPPORTING FUNCTIONS -----------------------------------------

	
	private function deletedir($dir) {
	//this is from php.net directly. Unmodified but works very well and quick.
	$this->load->helper('file');
  @delete_files($dir, TRUE);
  @rmdir($dir);
}

	
	private function getlangfiles($folder){
	$files = glob($folder . "*.php");
	$files = array_filter($files, function($a) {
    return substr($a,-9)=='_lang.php';
});
	return $files;
	}
	
	
//---------------------------------------------------------------	
}
