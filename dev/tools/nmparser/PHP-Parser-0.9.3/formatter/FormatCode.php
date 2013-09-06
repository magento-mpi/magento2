<?php

require '../lib/bootstrap.php';
require './PSR2.php';
$parser        = new PHPParser_Parser(new PHPParser_Lexer);
$prettyPrinter = new PSR2;
$path = 'printthesefiles/';
$path = '';
//$one_filename = '../../../../../app/code/Magento/Catalog/sql/catalog_setup/install-1.6.0.0.php';
//$one_filename = '../../../../../tests.php';
//echo "exists? $one_filename " . file_exists($one_filename) . "\n";
//$one_filename = 'D:\_workspaces\polarseals\magento2\app\code\Magento\Catalog\sql\catalog_setup\install-1.6.0.0.php';
//$filename = 'FooInterface.php';
//$filenames = array('Inline.php', 'BarClass.php', 'Foo.php','FooInterface.php');
//$filenames = array($one_filename);


function scandir_complete($dir)
{
    $items = glob($dir . '/*');

    for ($i = 0; $i < count($items); $i++) {
        if (is_dir($items[$i])) {
            $add = glob($items[$i] . '/*');
            $items = array_merge($items, $add);
        }
    }

    return $items;
}

//try {
#Run against an entire directory
$dir = 'C:/_workspaces/magento2/app/code/Magento';
$files = scandir_complete($dir);
# Run one file
//$files = array('C:/_workspaces/magento2/app/code/Magento/Catalog/Block/Product/View/Options/Type/Select.php');
echo "file count: " . count($files) . "\n";


$fileCount = 0;
foreach ($files as $filename) {
    if (!is_dir($filename) && preg_match('/\.php$/', $filename)) {
        echo ++$fileCount . ": examine $filename\n";
        $code = file_get_contents($path . $filename);
        // parse
        $stmts = $parser->parse($code);
        // pretty print
        $code = "<?php \n" . $prettyPrinter->prettyPrint($stmts) . "\n";
        echo "writing output to " . $filename . "\n";
        file_put_contents($filename, $code);
    }
}

//} catch (PHPParser_Error $e) {
//  echo 'Parse Error: ', $e->getMessage();
//}