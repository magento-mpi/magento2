<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

class PrinterReferenceTest extends TestBase
{
    /**
     * This method tests for various reference elements.
     *
     * @dataProvider dataReferences
     */
    public function testReferences($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    /**
     * Provide data to test method
     *
     * @return array
     */
    public function dataReferences()
    {
        return array(
            array(
                "<?php class C1 {const ALPHA='a'; public function a(){echo self::ALPHA;}}",
                "<?php\nclass C1\n{\n    const ALPHA = 'a';\n\n    public function a()\n    {\n" .
                "        echo self::ALPHA;\n    }\n}\n"
            ),
            array("<?php \$myarray[]=1;", "<?php\n\$myarray[] = 1;\n"),
            array("<?php \$myarray[0]=1;", "<?php\n\$myarray[0] = 1;\n"),
            array("<?php \$myarray['black']=1;", "<?php\n\$myarray['black'] = 1;\n"),
            array("<?php \$newobj=new \\Blah();", "<?php\n\$newobj = new \\Blah();\n"),
            array("<?php \$newobj=new \\Blah(1);", "<?php\n\$newobj = new \\Blah(1);\n"),
            array(
                "<?php \$newobj=new \\Blah(123456,123456,123456,123456,123456,123456,123456,123456,123456,123456," .
                "123456,123456,123456,123456);",
                "<?php\n\$newobj = new \\Blah(\n    123456,\n    123456,\n    123456,\n    123456,\n    123456,\n" .
                "    123456,\n    123456,\n    123456,\n    123456,\n    123456,\n    123456,\n    123456," .
                "\n    123456,\n    123456\n);\n"
            ),
            array("<?php empty(\$a);", "<?php\nempty(\$a);\n"),
            array("<?php eval(\$a);", "<?php\neval(\$a);\n"),
            array("<?php clone(\$a);", "<?php\nclone \$a;\n"),
            array("<?php print(\$a);", "<?php\nprint \$a;\n"),
            array("<?php exit('Bye');", "<?php\nexit('Bye');\n"),
            array("<?php isset(\$Bye);", "<?php\nisset(\$Bye);\n"),
            array("<?php if (isset(\$Bye)) { echo 'b'; } ", "<?php\nif (isset(\$Bye)) {\n    echo 'b';\n}\n"),
            array("<?php \$a = \"Is this \$encapsed\\n\";", "<?php\n\$a = \"Is this {\$encapsed}\\n\";\n"),
            array(
                "<?php if (true) {\$a = \"Is this \$encapsed\\n\";}",
                "<?php\nif (true) {\n    \$a = \"Is this {\$encapsed}\\n\";\n}\n"
            ),
            array("<?php \$a = 'Is this \$encapsed\\n';", "<?php\n\$a = 'Is this \$encapsed\\n';\n"),
            array("<?php \$a = `ls -l \$dir`;", "<?php\n\$a = `ls -l {\$dir}`;\n"),
            array(
                "<?php if (true) {\$a = 'Is this \$encapsed\\n';\nclone \$encapsed;}",
                "<?php\nif (true) {\n    \$a = 'Is this \$encapsed\\n';\n    clone \$encapsed;\n}\n"
            ),
            array(
                "<?php if ( !self::\$_instance ){self::\$_instance=null;}",
                "<?php\nif (!self::\$_instance) {\n    self::\$_instance = null;\n}\n"
            )
        );
    }
}
