# Foundry: Generate strings from molds and data

1. Requirement: *PHP 8*  
Either https://www.php.net/downloads.php or https://windows.php.net/download

1. Read the [License](./LICENSE.txt)

1. For Instructions on how to set up and use Foundry in your project, read the [INSTRUCTIONS](INSTRUCTIONS.md) file.

---
## Usage

The main method is  
```
Cast::pour(string $moldName, array $liquid, bool $useHtmlSpecialChars=true, int $depth=0 ) : Results
```

### Input parameters:  

1. The moldName is the name of the file containing the mold.

1. Liquid can be either:
    * An associative array of name/value pairs. Each key is a string, the name of a variable, and the value is the variable's value. OR
    * An associative array where each key is a number, and each value is an associative array of name/value pairs.

1. useHtmlSpecialChars is a bool which determines if the variables' values are process with *htmlspecialchars()*.

1. Depth is the number of levels of recursive calls to the pour() function.

### Return Value

Results is a helper class which wraps up multiple values into an object so that a function can return more than one value.

Results has methods like getError() and getInfo() to retrieve the values within it.

---
## Features

* Lets you generate more than html code. You can cast anything you like, including plain text.
* Mostly native PHP. You don't need to learn another language. The only exception to this is if you want to include multiple rows of data, you'll need to use the {pour $data 'moldfile'} syntax in your mold, to recursively pour data within a larger array of data into another moldfile.
* You can "chain" mold results together, i.e. use the output of *pour()* as input into a subsequent call to *pour().*
* Sample molds are provided for generating md and html formats. See the ['resources'](./resources) folder.
* Tests are available by running tests\runtests.bat from the command line.