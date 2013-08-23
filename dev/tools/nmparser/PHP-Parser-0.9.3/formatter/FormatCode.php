<?php

require '../lib/bootstrap.php';
require './PSR2.php';
$parser        = new PHPParser_Parser(new PHPParser_Lexer);
$prettyPrinter = new PSR2;
$path = 'printthesefiles/';
$path = '';
$one_filename = '../../../../../app/code/Magento/Catalog/sql/catalog_setup/install-1.6.0.0.php';
//$one_filename = '../../../../../tests.php';
echo "exists? $one_filename " . file_exists($one_filename) . "\n";
//$one_filename = 'D:\_workspaces\polarseals\magento2\app\code\Magento\Catalog\sql\catalog_setup\install-1.6.0.0.php';
//$filename = 'FooInterface.php';
//$filenames = array('Inline.php', 'BarClass.php', 'Foo.php','FooInterface.php');
$filenames = array($one_filename);
try {
    foreach ($filenames as $filename) {

        $code = file_get_contents($path . $filename);
        //$code = "<?php echo 'Hi ', hi\\getTarget();";

        // parse
        $stmts = $parser->parse($code);
        //echo "first statement:" . $stmts[0]->getType();
        // pretty print
        $code = "<?php \n" . $prettyPrinter->prettyPrint($stmts) . "\n";
        //$codeX = preg_replace("\\r", "", "ok");
        //echo $codeX . "\n";
        echo 'output to ' . $filename . '.f.php\n';
        file_put_contents($filename . '.f.php', $code);
        //echo $code;
    }
} catch (PHPParser_Error $e) {
    echo 'Parse Error: ', $e->getMessage();
}