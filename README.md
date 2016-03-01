# CodeIgniter-Language-Manager
Manage languages in use by codeigniter applications


This is made public from a recent application written in CI. It was part of the CMS and uses MyLang controller and mylangv view and mylang_lang language file (english edition). It is ajax driven with all the javascript going in mylangv directly.

Requires: JQUERY

To call it, place Mylang to your controller directory ; mylangv to your views folder, mylang_lang to your language folder in english and the CSS and png files to your desired folders.

Call it like anyother controller. In case you use it in admin area, like my application, you have to hack the mylang view file and edit the ajax calls which are in the form of:

url:<?php echo base_url();?>mylang/method;

to the correct path.

Known Issues:

Input is all santized, albiet weakly, as string. You can implement your own data purification in Mylang/savemodule.

Used Terms:

Language refers to the whole language  directory
Module individual language files in a language folder.
