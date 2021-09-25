# Foundry: Cast (generate) strings from molds and data

1. Requirement: *PHP 8*  
Either https://www.php.net/downloads.php or https://windows.php.net/download

1. Read the [License](./LICENSE.txt)

1. For Instructions on how to set up and use Foundry in your project, read the [INSTRUCTIONS](INSTRUCTIONS.md) file.

---
## Basic functionality

To quickly understand what this does, look at files
* [resources/simple_example](resources/simple_example.txt) (the mold) and
* [tests/simple_example.php](tests/simple_example.php) (a controller)

These files show how to generate some simple filled-in plain text.

To run simple_example,
1. Open a command line window.
1. Change working directory to the tests subfolder.
1. Run command:
    > php simple_example.php

---
## Usage

The main method is  
```
Cast::pour(string $moldName, array $liquid, bool $useHtmlSpecialChars=true ) : Results
```

### Input parameters:  

1. The moldName is the name of the file containing the mold.

1. Liquid can be either:
    * An associative array of name/value pairs. Each key is a string, the name of a variable, and the value is the variable's value. OR
    * An associative array where each key is a number, and each value is an associative array of name/value pairs.

1. useHtmlSpecialChars is a bool which determines if the variables' values are process with *htmlspecialchars()*.

### Return Value

Results is a helper class which wraps up multiple values into an object so that a function can return more than one value.

Results has methods like getError() and getInfo() to retrieve the values within it.

---
## Features

* Lets you generate more than html code. You can cast anything you like, including plain text.
* Native PHP. You don't need to learn another language. Future phases of this project may change that.
* Simple syntax. Use {$variable} in molds to replace the placeholder with the value of $variable.
* You can "chain" mold results together, i.e. use the output of *pour()* as input into a subsequent call to *pour().*
* Use the ToArray class to adapt data into an array suitable for sending into the *pour()* method.
* Sample molds are provided for generating text, md, and html formats. See the ['resources'](./resources) folder.
* Sample input data are provided. See ['tests/data'](./tests/data) folder.
* Tests are available by running *php tests/main.php* from the command line. Note that one of the tests intentionally produces errors to show that those errors are being caught.
