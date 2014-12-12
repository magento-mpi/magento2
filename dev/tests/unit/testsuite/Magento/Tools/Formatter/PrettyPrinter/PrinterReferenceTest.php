<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
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
        return [
            [
                "<?php class C1 {const ALPHA='a'; public function a(){echo self::ALPHA;}}",
                "<?php\nclass C1\n{\n    const ALPHA = 'a';\n\n    public function a()\n    {\n" .
                "        echo self::ALPHA;\n    }\n}\n",
            ],
            ["<?php \$myarray[]=1;", "<?php\n\$myarray[] = 1;\n"],
            ["<?php \$myarray[0]=1;", "<?php\n\$myarray[0] = 1;\n"],
            ["<?php \$myarray['black']=1;", "<?php\n\$myarray['black'] = 1;\n"],
            ["<?php \$newobj=new \\Blah();", "<?php\n\$newobj = new \\Blah();\n"],
            ["<?php \$newobj=new \\Blah(1);", "<?php\n\$newobj = new \\Blah(1);\n"],
            [
                "<?php \$newobj=new \\Blah(123456,123456,123456,123456,123456,123456,123456,123456,123456,123456," .
                "123456,123456,123456,123456);",
                "<?php\n\$newobj = new \\Blah(\n    123456,\n    123456,\n    123456,\n    123456,\n    123456,\n" .
                "    123456,\n    123456,\n    123456,\n    123456,\n    123456,\n    123456,\n    123456," .
                "\n    123456,\n    123456\n);\n"
            ],
            ["<?php empty(\$a);", "<?php\nempty(\$a);\n"],
            ["<?php eval(\$a);", "<?php\neval(\$a);\n"],
            ["<?php clone(\$a);", "<?php\nclone \$a;\n"],
            ["<?php print(\$a);", "<?php\nprint \$a;\n"],
            ["<?php exit('Bye');", "<?php\nexit('Bye');\n"],
            ["<?php isset(\$Bye);", "<?php\nisset(\$Bye);\n"],
            ["<?php if (isset(\$Bye)) { echo 'b'; } ", "<?php\nif (isset(\$Bye)) {\n    echo 'b';\n}\n"],
            ["<?php \$a = \"Is this \$encapsed\\n\";", "<?php\n\$a = \"Is this {\$encapsed}\\n\";\n"],
            [
                "<?php if (true) {\$a = \"Is this \$encapsed\\n\";}",
                "<?php\nif (true) {\n    \$a = \"Is this {\$encapsed}\\n\";\n}\n"
            ],
            ["<?php \$a = 'Is this \$encapsed\\n';", "<?php\n\$a = 'Is this \$encapsed\\n';\n"],
            ["<?php \$a = `ls -l \$dir`;", "<?php\n\$a = `ls -l {\$dir}`;\n"],
            [
                "<?php if (true) {\$a = 'Is this \$encapsed\\n';\nclone \$encapsed;}",
                "<?php\nif (true) {\n    \$a = 'Is this \$encapsed\\n';\n    clone \$encapsed;\n}\n"
            ],
            [
                "<?php if ( !self::\$_instance ){self::\$_instance=null;}",
                "<?php\nif (!self::\$_instance) {\n    self::\$_instance = null;\n}\n"
            ]
        ];
    }
}
