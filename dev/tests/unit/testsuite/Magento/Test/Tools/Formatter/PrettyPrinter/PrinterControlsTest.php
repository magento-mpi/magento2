<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Printer;

class PrinterControlsTest extends TestBase
{
    /**
     * This method tests for loops.
     *
     * @dataProvider dataLoops
     */
    public function testLoops($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    /**
     * Provide data to test method
     *
     * @return array
     */
    public function dataLoops()
    {
        return array(
            array(
                "<?php class F1 {public function a(){foreach (\$as as \$k=>\$a){echo 'hi';}}}",
                "<?php\nclass F1\n{\n    public function a()\n    {\n        foreach (\$as as \$k => \$a) {\n" .
                "            echo 'hi';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class F2 {public function b(){for(\$a;\$a;\$a){echo 'hi';}}}",
                "<?php\nclass F2\n{\n    public function b()\n    {\n        for (\$a; \$a; \$a) {\n" .
                "            echo 'hi';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class L1 {public function a(){while(\$a){echo 'hi';}}}",
                "<?php\nclass L1\n{\n    public function a()\n    {\n        while (\$a) {\n" .
                "            echo 'hi';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class L2 {public function a(){do{echo 'hi';}while(\$a);}}",
                "<?php\nclass L2\n{\n    public function a()\n    {\n        do {\n" .
                "            echo 'hi';\n        } while (\$a);\n    }\n}\n"
            ),
            array(
                "<?php class L2 {public function a(){try{echo 'hi';}catch(\\Exception \$e){echo 'lo';}}}",
                "<?php\nclass L2\n{\n    public function a()\n    {\n        try {\n" .
                "            echo 'hi';\n        } catch (\\Exception \$e) {\n            echo 'lo';\n        }\n" .
                "    }\n}\n"
            ),
        );
    }

    /**
     * This method tests for the if loops.
     *
     * @dataProvider dataIf
     */
    public function testIf($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    /**
     * Provide data to test method
     *
     * @return array
     */
    public function dataIf()
    {
        return array(
            array(
                "<?php class If1 {public function a(){if (\$b) {echo 'hi';}}}",
                "<?php\nclass If1\n{\n    public function a()\n    {\n        if (\$b) {\n" .
                "            echo 'hi';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class If2 {public function b(){if (\$b) {echo 'hi';}elseif (\$c){echo 'lo';}}}",
                "<?php\nclass If2\n{\n    public function b()\n    {\n        if (\$b) {\n" .
                "            echo 'hi';\n        } elseif (\$c) {\n            echo 'lo';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class If3 {public function b(){if (\$b) {echo 'hi';}else{echo 'lo';}}}",
                "<?php\nclass If3\n{\n    public function b()\n    {\n        if (\$b) {\n" .
                "            echo 'hi';\n        } else {\n            echo 'lo';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class If4 {public function d(){if (\$b) {echo 'hi';}elseif (\$c){echo 'lo';}else{echo 'e';}}}",
                "<?php\nclass If4\n{\n    public function d()\n    {\n        if (\$b) {\n" .
                "            echo 'hi';\n        } elseif (\$c) {\n            echo 'lo';\n        } else {\n" .
                "            echo 'e';\n        }\n    }\n}\n"
            ),
            array(
                "<?php class Sc1 {public function a(){switch(\$a){case 1:break;default:break;}}}",
                "<?php\nclass Sc1\n{\n    public function a()\n    {\n        switch (\$a) {\n" .
                "            case 1:\n                break;\n            default:\n                break;\n" .
                "        }\n    }\n}\n"
            ),
        );
    }

    /**
     * This method tests for controls.
     *
     * @dataProvider dataControls
     */
    public function testControls($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    /**
     * Provide data to test method
     *
     * @return array
     */
    public function dataControls()
    {
        return array(
            array(
                "<?php class Con1 {public function a(){array_merge(\$a,function(){echo 'hi';});}}",
                "<?php\nclass Con1\n{\n    public function a()\n    {\n" .
                "        array_merge(\n            \$a,\n            function () {\n                echo 'hi';\n" .
                "            }\n        );\n    }\n}\n"
            ),
            array(
                "<?php class Con2 {public function a(){array_merge(\$a,function()use(\$a,\$b){echo 'hi';});}}",
                "<?php\nclass Con2\n{\n    public function a()\n    {\n        array_merge(\n            \$a,\n" .
                "            function () use (\$a, \$b) {\n                echo 'hi';\n" .
                "            }\n        );\n    }\n}\n"
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
namespace Magento\Tools\Formatter\TestClass;
class TestClass {
    public function main(\$abcdefghijklmnopqrstuvwxyz) {
        if (isset(\$abcdefghijklmnopqrstuvwxyz)&&isset(\$abcdefghijklmnopqrstuvwxyz)&&
            isset(\$abcdefghijklmnopqrstuvwxyz)) {
            \$callback = 'hello';
            \$callback = 'good';
            \$callback = 'bye';
            if (isset(\$abcdefghijklmnopqrstuvwxyz)) {
                \$callback = 'asdf';
            }
        }
    }
}
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
<?php
namespace Magento\Tools\Formatter\TestClass;

class TestClass
{
    public function main(\$abcdefghijklmnopqrstuvwxyz)
    {
        if (
            isset(\$abcdefghijklmnopqrstuvwxyz) &&
            isset(\$abcdefghijklmnopqrstuvwxyz) &&
            isset(\$abcdefghijklmnopqrstuvwxyz)
        ) {
            \$callback = 'hello';
            \$callback = 'good';
            \$callback = 'bye';
            if (isset(\$abcdefghijklmnopqrstuvwxyz)) {
                \$callback = 'asdf';
            }
        }
    }
}

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
namespace Magento\Tools\Formatter\TestClass;
class TestClass {
    public function main(\$results) {
        if (strcasecmp('FALSE', \$results) === 0 || strcasecmp('TRUE', \$results) === 0 ||
            strcasecmp('NULL', \$results) === 0) {
            \$tokens[sizeof(\$tokens) - 1] = strtolower(\$results);
            // reset the last item in the array due to php's "copy-on-write" rule for arrays
            \$treeNode->getData()->line->setTokens(\$tokens);
        }
    }
}
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
<?php
namespace Magento\Tools\Formatter\TestClass;

class TestClass
{
    public function main(\$results)
    {
        if (
            strcasecmp('FALSE', \$results) ===
            0 ||
            strcasecmp('TRUE', \$results) ===
            0 ||
            strcasecmp('NULL', \$results) ===
            0
        ) {
            \$tokens[sizeof(\$tokens) - 1] = strtolower(\$results);
            // reset the last item in the array due to php's "copy-on-write" rule for arrays
            \$treeNode->getData()->line->setTokens(\$tokens);
        }
    }
}

FORMATTEDCODESNIPPET
            ),
        );
    }
}