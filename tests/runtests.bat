@echo off
rem ##########################################
rem     There are 6 tests
rem ##########################################

echo Generating markdown (.md)
echo.

echo input is data\stores_data.json:
echo.
php testjson.php   data\stores_data.json   ..\resources\md_doc

echo input is data\stores.sqlite:
echo.
php testsqlite.php data\stores.sqlite      ..\resources\md_doc

echo input is data\stores_fruits.csv:
echo.
php testcsv.php    data\stores_fruits.csv  ..\resources\md_doc

rem ##########################################

echo Generating html (.html)
echo.

echo input is data\stores_data.json:
echo.
php testjson.php   data\stores_data.json   ..\resources\html_doc

echo input is data\stores.sqlite:
echo.
php testsqlite.php data\stores.sqlite      ..\resources\html_doc

echo input is data\stores_fruits.csv:
echo.
php testcsv.php    data\stores_fruits.csv  ..\resources\html_doc
