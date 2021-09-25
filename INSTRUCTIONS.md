INSTRUCTIONS:

1. Download the Foundry zip file from https://github.com/LMurphy001/Foundry/releases

1. Unpack the zip file somewhere

1. Copy tests/LMFoundry_autoload.php to your project folder

1. Figure out the path to Foundry's 'src' sub-folder. You can see how the original file in the tests sub-folder accessed the 'src' sub-folder.

1. Change the path in your copy of LMFoundry_autoload.php to match the path of Foundry's 'src' sub-folder.

1. In your code, add a require for the LMFoundry_autoload.php file. For example:
   > require_once 'LMFoundry_autoload.php';

1. In any file which needs Foundry's classes, add a 'use' statement to access them. For example:
   > use LM\Foundry\{Config, Cast};

1. Edit Foundry's config/config.ini file. Change MOLD_DIR to be the path to your own folder. This can be above the folder of your project, *as long as it is relative to the 'src' sub-folder of Foundry.*

1. Write some molds and put them in your molds folder.

1. Write code to use the Mold method 'pour().' 
   The README file describes the inputs and outputs of pour().
    
1. Make sure that the keys of the associative array(s) you input
   to pour() match the {$variable} placeholder names in the mold files you've writen.

1. If a mold contains a placeholder name, then $liquid MUST contain that key. If you fail to put it in the array, you will see an error such as
   > Missing value for ...

1. It's fine to have keys in $liquid which are not in your mold. They'll be ignored.

1. The placeholder names must only consist of letters, digits, and the underscore '_' character. If you use any other characters, you will see an error such as
   > Badly formed variable name ...

1. You can 'chain' molds together. The output of filling in one mold can be paired with a key in a new associative array. That array can then be sent as input to the *pour()* method along with a mold which has a placeholder for that key.

1. See the [tests](./tests) subfolder for examples of how to use Foundry.
