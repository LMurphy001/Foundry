@echo off
cls

rem php testcsv.php  data\csv\store_list.csv ..\resources\html_doc
rem pause

php testjson.php   data\stores_data.json   ..\resources\md_doc
php testsqlite.php data\stores.sqlite      ..\resources\md_doc

rem pause
rem pause
rem php testjson.php data\stores_data.json ..\resources\md_doc 
