<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

class PrinterTest extends TestBase
{
    /**
     * This method tests arrays in the pretty printer.
     *
     * @dataProvider dataArrays
     */
    public function testArrays($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    /**
     * Provide data to test method
     *
     * @return array
     */
    public function dataArrays()
    {
        return [
            ["<?php class A1 {public \$a = array();}", "<?php\nclass A1\n{\n    public \$a = array();\n}\n"],
            [
                "<?php class A2 {public \$a2 = array(1,2,3,4,5);}",
                "<?php\nclass A2\n{\n    public \$a2 = array(1, 2, 3, 4, 5);\n}\n"
            ],
            [
                "<?php class A3 {public \$a3 = array(1=>'alpha',2=>'beta',3=>'gamma',4=>'hippopotamus',5=>'giraffe');}",
                "<?php\nclass A3\n{\n    public \$a3 = array(1 => 'alpha', 2 => 'beta', 3 => 'gamma', " .
                "4 => 'hippopotamus', 5 => 'giraffe');\n}\n"
            ],
            [
                "<?php class A4 {public \$a3 = array(1=>'alpha1234567890',2=>'beta1234567890',3=>'gamma1234567890'," .
                "4=>'hippopotamus',5=>'giraffe');}",
                "<?php\nclass A4\n{\n    public \$a3 = array(\n        1 => 'alpha1234567890',\n" .
                "        2 => 'beta1234567890',\n        3 => 'gamma1234567890',\n" .
                "        4 => 'hippopotamus',\n        5 => 'giraffe'\n    );\n}\n"
            ],
            [
                "<?php class A5 {public \$a = array(array(1.1,1.2),array(2.1,2.2));}",
                "<?php\nclass A5\n{\n    public \$a = array(array(1.1, 1.2), array(2.1, 2.2));\n}\n"
            ],
            [
                "<?php class A6 {public \$a = array(array('abcdefghijabcdefghijabcdefghij','abcdefghijabcdefghij')," .
                "array('abcdefghijabcdefghijabcdefghij','abcdefghijabcdefghij'),);}",
                "<?php\nclass A6\n{\n    public \$a = array(\n        array(" .
                "'abcdefghijabcdefghijabcdefghij', 'abcdefghijabcdefghij')," .
                "\n        array('abcdefghijabcdefghijabcdefghij', 'abcdefghijabcdefghij')\n    );\n}\n"
            ]
        ];
    }

    /**
     * This method tests some of the basics of the pretty printer.
     *
     * @dataProvider dataProviderBasics
     */
    public function testBasics($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    /**
     * Provide data to test method
     *
     * @return array
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function dataProviderBasics()
    {
        $originalCodeSnippet0 = <<<'ORIGINALCODESNIPPET0'
<?php
/**
 * Class Foo
 */
class Foo extends Bar implements Zulu {
    /** alpha method */
    public function alpha() {
        return $this;
    }
    /** beta method */
    public function beta() {
        // Property call
        echo $that->{$this->getName()};
        // Method call
        echo $that->{$this->getName()}();
        return $this->alpha()->alpha()->alpha()->alpha()->alpha()->alpha()->alpha()->alpha()
            ->alpha()->alpha()->alpha()->alpha();
    }
}
ORIGINALCODESNIPPET0;
        $formattedCodeSnippet0 = <<<'FORMATTEDCODESNIPPET0'
<?php
/**
 * Class Foo
 */
class Foo extends Bar implements Zulu
{
    /** alpha method */
    public function alpha()
    {
        return $this;
    }

    /** beta method */
    public function beta()
    {
        // Property call
        echo $that->{$this->getName()};
        // Method call
        echo $that->{$this->getName()}();
        return $this->alpha()
            ->alpha()
            ->alpha()
            ->alpha()
            ->alpha()
            ->alpha()
            ->alpha()
            ->alpha()
            ->alpha()
            ->alpha()
            ->alpha()
            ->alpha();
    }
}

FORMATTEDCODESNIPPET0;
        $originalCodeSnippet1 = <<<'ORIGINALCODESNIPPET1'
<?php
  namespace Magento\Test;
class Foo {
    function x() {
        // bob
        $d = 1+1;

    }
};
ORIGINALCODESNIPPET1;
        $formattedCodeSnippet1 = <<<'FORMATTEDCODESNIPPET1'
<?php
namespace Magento\Test;

class Foo
{
    public function x()
    {
        // bob
        $d = 1 + 1;
    }
}

FORMATTEDCODESNIPPET1;
        $originalCodeSnippet2 = <<<'ORIGINALCODESNIPPET2'
<?php
  namespace Magento\Test;
class Foo {
    function x() {
        $d = 1+1;

    }
};
ORIGINALCODESNIPPET2;
        $formattedCodeSnippet2 = <<<'FORMATTEDCODESNIPPET2'
<?php
namespace Magento\Test;

class Foo
{
    public function x()
    {
        $d = 1 + 1;
    }
}

FORMATTEDCODESNIPPET2;
        $originalCodeSnippet3 = <<<'ORIGINALCODESNIPPET3'
<?php
  namespace Magento\Test;
class Foo {
    function x() {
        unset($d);

    }
};
ORIGINALCODESNIPPET3;
        $formattedCodeSnippet3 = <<<'FORMATTEDCODESNIPPET3'
<?php
namespace Magento\Test;

class Foo
{
    public function x()
    {
        unset($d);
    }
}

FORMATTEDCODESNIPPET3;
        $originalCodeSnippet4 = <<<ORIGINALCODESNIPPET4
<?php
  namespace Magento\\Test;
class Foo {
    function x() {
        throw new \Exception('error');

    }
};
ORIGINALCODESNIPPET4;
        $formattedCodeSnippet4 = <<<FORMATTEDCODESNIPPET4
<?php
namespace Magento\\Test;

class Foo
{
    public function x()
    {
        throw new \Exception('error');
    }
}

FORMATTEDCODESNIPPET4;
        $originalCodeSnippet5 = <<<'ORIGINALCODESNIPPET5'
<?php
require_once __DIR__.'processor.php';
require __DIR__.'processor.php';
require_once __FILE__.'processor.php';
require __FILE__.'processor.php';
require_once __METHOD__.'processor.php';
require __METHOD__.'processor.php';
require_once 'processor.php';
require('processor.php');
require_once('processor.php');
require 'processor.php';
require_once 'processor'.'.php';
require('processor'.'.php');
require_once('processor'.'.php');
require 'processor'.'.php';

$processor = 1+2;


ORIGINALCODESNIPPET5;
        $formattedCodeSnippet5 = <<<'FORMATTEDCODESNIPPET5'
<?php
require_once __DIR__ . 'processor.php';
require __DIR__ . 'processor.php';
require_once __FILE__ . 'processor.php';
require __FILE__ . 'processor.php';
require_once __METHOD__ . 'processor.php';
require __METHOD__ . 'processor.php';
require_once 'processor.php';
require 'processor.php';
require_once 'processor.php';
require 'processor.php';
require_once 'processor' . '.php';
require 'processor' . '.php';
require_once 'processor' . '.php';
require 'processor' . '.php';

$processor = 1 + 2;

FORMATTEDCODESNIPPET5;
        $originalCodeSnippet6 = <<<'ORIGINALCODESNIPPET6'
<?php
  namespace Magento\Test;
class Foo {
    function x() {
        $d=1+1;
        echo 1 + (2 - 2) * 3;

    }
};
ORIGINALCODESNIPPET6;
        $formattedCodeSnippet6 = <<<'FORMATTEDCODESNIPPET6'
<?php
namespace Magento\Test;

class Foo
{
    public function x()
    {
        $d = 1 + 1;
        echo 1 + (2 - 2) * 3;
    }
}

FORMATTEDCODESNIPPET6;
        $originalCodeSnippet7 = <<<ORIGINALCODESNIPPET7
<?php
  namespace Magento\\Test;

/**
 * Some stuff
 */

class Foo {
    const ATTRIBUTE_COMMENTS = 'comments';

    const ATTRIBUTE_COMMENTS = 'comments';

    function x() {
        echo 'y';

        echo 'z';
    }
};
ORIGINALCODESNIPPET7;
        $formattedCodeSnippet7 = <<<FORMATTEDCODESNIPPET7
<?php
namespace Magento\\Test;

/**
 * Some stuff
 */
class Foo
{
    const ATTRIBUTE_COMMENTS = 'comments';

    const ATTRIBUTE_COMMENTS = 'comments';

    public function x()
    {
        echo 'y';

        echo 'z';
    }
}

FORMATTEDCODESNIPPET7;
        $originalCodeSnippet8 = <<<ORIGINALCODESNIPPET8
<?php
  namespace Magento\\Test;



/**
 * Some stuff
 */



class Foo {
    const ATTRIBUTE_COMMENTS = 'comments';

    const ATTRIBUTE_COMMENTS = 'comments';

    function x() {
        echo 'y';

        echo 'z';
    }
};
ORIGINALCODESNIPPET8;
        $formattedCodeSnippet8 = <<<FORMATTEDCODESNIPPET8
<?php
namespace Magento\\Test;

/**
 * Some stuff
 */
class Foo
{
    const ATTRIBUTE_COMMENTS = 'comments';

    const ATTRIBUTE_COMMENTS = 'comments';

    public function x()
    {
        echo 'y';

        echo 'z';
    }
}

FORMATTEDCODESNIPPET8;
        $originalCodeSnippet9 = <<<ORIGINALCODESNIPPET9
<?php
  namespace Magento\\Test;


class Foo {
    function x() {
        echo 'y';

        echo 'z';
    }
};
ORIGINALCODESNIPPET9;
        $formattedCodeSnippet9 = <<<FORMATTEDCODESNIPPET9
<?php
namespace Magento\\Test;

class Foo
{
    public function x()
    {
        echo 'y';

        echo 'z';
    }
}

FORMATTEDCODESNIPPET9;
        $originalCodeSnippet10 = <<<ORIGINALCODESNIPPET10
<?php
  namespace Magento\\Test;
class Foo {
    function x() {
        echo 'y';

        echo 'z';

    }
};
ORIGINALCODESNIPPET10;
        $formattedCodeSnippet10 = <<<FORMATTEDCODESNIPPET10
<?php
namespace Magento\\Test;

class Foo
{
    public function x()
    {
        echo 'y';

        echo 'z';
    }
}

FORMATTEDCODESNIPPET10;
        $originalCodeSnippet11 = <<<ORIGINALCODESNIPPET11
<?php
  namespace Magento\\Test;
class Foo {
    function x() {
        // Comment at start of block
        echo 'y';
        echo 'z';
        // Comment at end of block
    }
};
ORIGINALCODESNIPPET11;
        $formattedCodeSnippet11 = <<<FORMATTEDCODESNIPPET11
<?php
namespace Magento\\Test;

class Foo
{
    public function x()
    {
        // Comment at start of block
        echo 'y';
        echo 'z';
    }
}

FORMATTEDCODESNIPPET11;
        $originalCodeSnippet12 = <<<ORIGINALCODESNIPPET12
<?php
  namespace Magento\\Test;
class Foo {
    function x() {
        // Comment at start of block

        // echo 'b';

        echo 'y' . 'e' . 's';
        echo 'somehing\\n'.(1+2).'more';

        echo 'z';
        echo "My super string \\n Is here".(1).'mixed'.\$varOne;
        // Comment at end of block
    }
};
ORIGINALCODESNIPPET12;
        $formattedCodeSnippet12 = <<<FORMATTEDCODESNIPPET12
<?php
namespace Magento\\Test;

class Foo
{
    public function x()
    {
        // Comment at start of block

        // echo 'b';

        echo 'y' . 'e' . 's';
        echo 'somehing\\n' . (1 + 2) . 'more';

        echo 'z';
        echo "My super string \\n Is here" . 1 . 'mixed' . \$varOne;
    }
}

FORMATTEDCODESNIPPET12;
        $originalCodeSnippet13 = <<<ORIGINALCODESNIPPET13
<?php
  namespace Magento\\Test;
class Foo {
    protected \$_constA = NULL;
    protected \$_constB = TRUE;
    protected \$_constC = FALSE;
    protected \$_constD = null;
    protected \$_constE = true;
    protected \$_constF = false;
    protected \$_constG = Null;
    protected \$_constH = True;
    protected \$_constI = False;
};
ORIGINALCODESNIPPET13;
        $formattedCodeSnippet13 = <<<FORMATTEDCODESNIPPET13
<?php
namespace Magento\\Test;

class Foo
{
    protected \$_constA = null;

    protected \$_constB = true;

    protected \$_constC = false;

    protected \$_constD = null;

    protected \$_constE = true;

    protected \$_constF = false;

    protected \$_constG = null;

    protected \$_constH = true;

    protected \$_constI = false;
}

FORMATTEDCODESNIPPET13;
        $originalCodeSnippet14 = <<<ORIGINALCODESNIPPET14
<?php
  namespace Magento\\Test;
class Foo {
    protected function _construct() {
        \$this->_testPropertyCall = 'test_text';
        \$this->_testFunctionCall = __('Testing');
    }
};
ORIGINALCODESNIPPET14;
        $formattedCodeSnippet14 = <<<FORMATTEDCODESNIPPET14
<?php
namespace Magento\\Test;

class Foo
{
    protected function _construct()
    {
        \$this->_testPropertyCall = 'test_text';
        \$this->_testFunctionCall = __('Testing');
    }
}

FORMATTEDCODESNIPPET14;
        $originalCodeSnippet15 = <<<ORIGINALCODESNIPPET15
<?php
  namespace Magento\\Test;
class Foo extends \\Magento\\Test\\Bar{
    public function __construct(
        \\Magento\\Test\\One \$testOne,
        \\Magento\\Test\\Two \$testTwo,
        \\Magento\\Test\\Three \$testThree,
        \\Magento\\Test\\Four \$testFour,
        \\Magento\\Test\\Five \$testFive,
        \\Magento\\Test\\Six \$testSix,
        \\Magento\\Test\\Seven \$testSeven,
        \\Magento\\Test\\Eight \$testEight,
        \\Magento\\Test\\Nine \$testNine
    ) {
        \$this->_testOne = \$testOne;
        parent::__construct(\$testTwo, \$testThree, \$testFour, \$testFive, \$testSix, \$testSeven, \$testSeven,
        \$testEight, \$testNine);
    }
};
ORIGINALCODESNIPPET15;
        $formattedCodeSnippet15 = <<<FORMATTEDCODESNIPPET15
<?php
namespace Magento\\Test;

class Foo extends \\Magento\\Test\\Bar
{
    public function __construct(
        \\Magento\\Test\\One \$testOne,
        \\Magento\\Test\\Two \$testTwo,
        \\Magento\\Test\\Three \$testThree,
        \\Magento\\Test\\Four \$testFour,
        \\Magento\\Test\\Five \$testFive,
        \\Magento\\Test\\Six \$testSix,
        \\Magento\\Test\\Seven \$testSeven,
        \\Magento\\Test\\Eight \$testEight,
        \\Magento\\Test\\Nine \$testNine
    ) {
        \$this->_testOne = \$testOne;
        parent::__construct(
            \$testTwo,
            \$testThree,
            \$testFour,
            \$testFive,
            \$testSix,
            \$testSeven,
            \$testSeven,
            \$testEight,
            \$testNine
        );
    }
}

FORMATTEDCODESNIPPET15;
        $originalCodeSnippet16 = <<<ORIGINALCODESNIPPET16
<?php
  namespace Magento\\Test;
class Foo{
    public function testList(\$testOne) {
        list(\$varOne, \$varTwo) = \$testOne;
    }
};
ORIGINALCODESNIPPET16;
        $formattedCodeSnippet16 = <<<FORMATTEDCODESNIPPET16
<?php
namespace Magento\\Test;

class Foo
{
    public function testList(\$testOne)
    {
        list(\$varOne, \$varTwo) = \$testOne;
    }
}

FORMATTEDCODESNIPPET16;
        $originalCodeSnippet17 = <<<ORIGINALCODESNIPPET17
<?php
  namespace Magento\\Test;
class Foo{
    public function testList(\$testOne) {
        list( \$varOne,\$varTwo,\$varThree,\$varFour,\$varFive,\$varSix,\$varSeven,\$varEight,\$varNine,\$varTen)
        = \$testOne;
    }
};
ORIGINALCODESNIPPET17;
        $formattedCodeSnippet17 = <<<FORMATTEDCODESNIPPET17
<?php
namespace Magento\\Test;

class Foo
{
    public function testList(\$testOne)
    {
        list(\$varOne,
            \$varTwo,
            \$varThree,
            \$varFour,
            \$varFive,
            \$varSix,
            \$varSeven,
            \$varEight,
            \$varNine,
            \$varTen) = \$testOne;
    }
}

FORMATTEDCODESNIPPET17;
        $originalCodeSnippet18 = <<<ORIGINALCODESNIPPET18
<?php
function foo(){
    if (true) {
?>
<html><body>Hi</body></html>
<?php
}
}
ORIGINALCODESNIPPET18;
        $formattedCodeSnippet18 = <<<FORMATTEDCODESNIPPET18
<?php
function foo()
{
    if (true) {
        ?>
        <html><body>Hi</body></html>
        <?php
    }
}

FORMATTEDCODESNIPPET18;

        return [
            [$originalCodeSnippet0, $formattedCodeSnippet0],
            [$originalCodeSnippet1, $formattedCodeSnippet1],
            [$originalCodeSnippet2, $formattedCodeSnippet2],
            [$originalCodeSnippet3, $formattedCodeSnippet3],
            [$originalCodeSnippet4, $formattedCodeSnippet4],
            [$originalCodeSnippet5, $formattedCodeSnippet5],
            [$originalCodeSnippet6, $formattedCodeSnippet6],
            [$originalCodeSnippet7, $formattedCodeSnippet7],
            [$originalCodeSnippet8, $formattedCodeSnippet8],
            [$originalCodeSnippet9, $formattedCodeSnippet9],
            [$originalCodeSnippet10, $formattedCodeSnippet10],
            [$originalCodeSnippet11, $formattedCodeSnippet11],
            [$originalCodeSnippet12, $formattedCodeSnippet12],
            [$originalCodeSnippet13, $formattedCodeSnippet13],
            [$originalCodeSnippet14, $formattedCodeSnippet14],
            [$originalCodeSnippet15, $formattedCodeSnippet15],
            [$originalCodeSnippet16, $formattedCodeSnippet16],
            [$originalCodeSnippet17, $formattedCodeSnippet17],
            [$originalCodeSnippet18, $formattedCodeSnippet18]
        ];
    }

    /**
     * This method tests the printing around classes.
     *
     * @dataProvider dataProviderClassDeclaration
     */
    public function testClassDeclaration($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    public function dataProviderClassDeclaration()
    {
        return [
            ["<?php class NoChildren {}", "<?php\nclass NoChildren\n{\n}\n"],
            ["<?php class JustParent extends Parent {}", "<?php\nclass JustParent extends Parent\n{\n}\n"],
            [
                "<?php class JustParent extends Fully\\Qualified\\Parent {}",
                "<?php\nclass JustParent extends Fully\\Qualified\\Parent\n{\n}\n"
            ],
            [
                "<?php class JustParent extends \\Fully\\Qualified\\Parent {}",
                "<?php\nclass JustParent extends \\Fully\\Qualified\\Parent\n{\n}\n"
            ],
            [
                "<?php class JustParent extends \\Fully\\Qualified\\Parent implements " .
                "\\Fully\\Qualified\\Long\\InterfaceName\\HelperInterface," .
                "\\Fully\\Qualified\\Long\\InterfaceName\\Iface {}",
                "<?php\nclass JustParent extends \\Fully\\Qualified\\Parent implements\n" .
                "    \\Fully\\Qualified\\Long\\InterfaceName\\HelperInterface,\n" .
                "    \\Fully\\Qualified\\Long\\InterfaceName\\Iface\n{\n}\n"
            ],
            [
                "<?php class JustParent implements \\Fully\\Qualified\\HelperInterface {}",
                "<?php\nclass JustParent implements \\Fully\\Qualified\\HelperInterface\n{\n}\n"
            ],
            [
                "<?php class JustInterface implements Interface1 {}",
                "<?php\nclass JustInterface implements Interface1\n{\n}\n"
            ],
            [
                "<?php class JustInterfaces implements Interface1, Interface2, Interface3 {}",
                "<?php\nclass JustInterfaces implements Interface1, Interface2, Interface3\n{\n}\n"
            ],
            [
                "<?php class ParentPlus extends Parent implements Interface1 {}",
                "<?php\nclass ParentPlus extends Parent implements Interface1\n{\n}\n"
            ],
            [
                "<?php class ParentPluses extends Parent implements Interface1, Interface2, Interface3 {}",
                "<?php\nclass ParentPluses extends Parent implements Interface1, Interface2, Interface3\n{\n}\n"
            ],
            [
                "<?php class BigParentPluses extends Parent implements Interface1, Interface2, Interface3, " .
                "Interface4,Interface5,Interface6,Interface7 {}",
                "<?php\nclass BigParentPluses extends Parent implements\n    Interface1,\n    Interface2,\n" .
                "    Interface3,\n    Interface4,\n    Interface5,\n    Interface6,\n    Interface7\n{\n}\n"
            ],
            ["<?php interface NoChildren {}", "<?php\ninterface NoChildren\n{\n}\n"],
            [
                "<?php interface JustParent extends Parent {}",
                "<?php\ninterface JustParent extends Parent\n{\n}\n"
            ],
            [
                "<?php interface BigParent extends Interface1,Interface2,Interface3,Interface4,Interface5" .
                ",Interface6,Interface7,Interface8,Interface9 {}",
                "<?php\ninterface BigParent extends\n    Interface1,\n    Interface2,\n    Interface3,\n" .
                "    Interface4,\n    Interface5,\n    Interface6,\n    Interface7,\n    Interface8,\n    Interface9" .
                "\n{\n}\n"
            ]
        ];
    }

    /**
     * This method tests the printing around namespace and use statements.
     *
     * @dataProvider dataProviderNamespace
     */
    public function testNamespace($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    public function dataProviderNamespace()
    {
        return [
            [
                "<?php /** filedoco */namespace LocalNamespace; class LocalClass {}",
                "<?php\n/** filedoco */\nnamespace LocalNamespace;\n\nclass LocalClass\n{\n}\n",
            ],
            [
                "<?php /** filedoco */namespace LocalNs; use SomethingElse; class LocalC2 {}",
                "<?php\n/** filedoco */\nnamespace LocalNs;\n\nuse SomethingElse;\n\nclass LocalC2\n{\n}\n"
            ],
            [
                "<?php /** filedoco */namespace LocalNs2; use WantingAlias as WAlias; class LocalC3 {}",
                "<?php\n/** filedoco */\nnamespace LocalNs2;\n\nuse WantingAlias as WAlias;\n\nclass LocalC3\n{\n}\n"
            ],
            [
                "<?php /** filedoco */namespace LocalNs2; use SE1, SE2, SE3; class LocalC4 {}",
                "<?php\n/** filedoco */\nnamespace LocalNs2;\n\nuse SE1;\nuse SE2;\nuse SE3;\n\nclass LocalC4\n{\n}\n"
            ]
        ];
    }

    /**
     * This method tests the printing around properties of a class.
     *
     * @dataProvider dataProviderProperties
     */
    public function testProperties($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    public function dataProviderProperties()
    {
        return [
            [
                "<?php class LocalClass {/** const doco */ const PI=3.14; /** member doc */ private \$foo='bar';}",
                "<?php\nclass LocalClass\n{\n    /** const doco */\n    const PI = 3.14;\n\n    /** member doc */\n" .
                "    private \$foo = 'bar';\n}\n",
            ],
            [
                "<?php class Local2 {/** const doco */ const PI=3.14,e=2.71828; /** const2 doco */ const mu=1;}",
                "<?php\nclass Local2\n{\n    /** const doco */\n    const PI = 3.14, e = 2.71828;\n\n" .
                "    /** const2 doco */\n    const mu = 1;\n}\n"
            ],
            [
                "<?php class Local3 {public static \$a;protected \$b;private \$c;public \$d,\$e,\$f;}",
                "<?php\nclass Local3\n{\n    public static \$a;\n\n    protected \$b;\n\n    private \$c;\n\n" .
                "    public \$d, \$e, \$f;\n}\n"
            ]
        ];
    }

    /**
     * This method tests the printing around method declarations.
     *
     * @dataProvider dataMethodDeclarations
     */
    public function testMethodDeclarations($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    public function dataMethodDeclarations()
    {
        $originalMethod0 = <<<'ORIGINALMETHOD0'
<?php class OM {
    public function testIsValid($attributeData, $result, $expected, $messages, $data = array('attribute' => 'new_test'))
    {}}
ORIGINALMETHOD0;
        $formattedMethod0 = <<<'FORMATTEDMETHOD0'
<?php
class OM
{
    public function testIsValid(
        $attributeData,
        $result,
        $expected,
        $messages,
        $data = array('attribute' => 'new_test')
    ) {
    }
}

FORMATTEDMETHOD0;
        $originalMethod1 = <<<'ORIGINALMETHOD1'
<?php class OM {
    public function testIsValid($attributeData, $result, $expected, $messages,
    $data = array('attribute' => 'new_test', 'longattribute' => 'abcdefghijabcdefghijabcdefghijabcdefghij',
    'longerattribute' => 'abcdefghijabcdefghijabcdefghijabcdefghijabcdefghijabcdefghijabcdefghijabcdefghij'))
    {}}
ORIGINALMETHOD1;
        $formattedMethod1 = <<<'FORMATTEDMETHOD1'
<?php
class OM
{
    public function testIsValid(
        $attributeData,
        $result,
        $expected,
        $messages,
        $data = array(
            'attribute' => 'new_test',
            'longattribute' => 'abcdefghijabcdefghijabcdefghijabcdefghij',
            'longerattribute' => 'abcdefghijabcdefghijabcdefghijabcdefghijabcdefghijabcdefghijabcdefghijabcdefghij'
        )
    ) {
    }
}

FORMATTEDMETHOD1;

        return [
            [$originalMethod0, $formattedMethod0],
            [$originalMethod1, $formattedMethod1],
            [
                "<?php class MD {public function alpha() {}}",
                "<?php\nclass MD\n{\n    public function alpha()\n    {\n    }\n}\n"
            ],
            [
                "<?php class MD2 {public function alpha(\$a) {}}",
                "<?php\nclass MD2\n{\n    public function alpha(\$a)\n    {\n    }\n}\n"
            ],
            [
                "<?php class MD3 {public function alpha(TestClass \$a) {}}",
                "<?php\nclass MD3\n{\n    public function alpha(TestClass \$a)\n    {\n    }\n}\n"
            ],
            [
                "<?php class MD4 {public function alpha(TestClass \$a,TestClass \$b) {}}",
                "<?php\nclass MD4\n{\n    public function alpha(TestClass \$a, TestClass \$b)\n    {\n    }\n}\n"
            ],
            [
                "<?php class MD5 {public function alpha(TestClass \$a,TestClass \$b,TestClass \$c,TestClass \$d," .
                "TestClass \$e,TestClass \$f,TestClass \$g,TestClass \$h) {}}",
                "<?php\nclass MD5\n{\n    public function alpha(\n        TestClass \$a,\n        TestClass \$b,\n" .
                "        TestClass \$c,\n        TestClass \$d,\n        TestClass \$e,\n        TestClass \$f,\n" .
                "        TestClass \$g,\n        TestClass \$h\n    ) {\n    }\n}\n"
            ]
        ];
    }

    /**
     * This method tests the printing around function declarations.
     *
     * @dataProvider dataFunctionDeclarations
     */
    public function testFunctionDeclarations($originalCode, $formattedCode)
    {
        $this->convertAndCheck($originalCode, $formattedCode);
    }

    public function dataFunctionDeclarations()
    {
        return [
            ["<?php function alpha() {}", "<?php\nfunction alpha()\n{\n}\n"],
            ["<?php function alpha(\$a) {}", "<?php\nfunction alpha(\$a)\n{\n}\n"],
            ["<?php function alpha(TestClass \$a) {}", "<?php\nfunction alpha(TestClass \$a)\n{\n}\n"],
            [
                "<?php function alpha(TestClass \$a,TestClass \$b) {}",
                "<?php\nfunction alpha(TestClass \$a, TestClass \$b)\n{\n}\n"
            ],
            [
                "<?php function alpha(TestClass12345 \$a,TestClass12345 \$b,TestClass12345 \$c,TestClass12345 \$d," .
                "TestClass12345 \$e,TestClass12345 \$f,TestClass12345 \$g) {}",
                "<?php\nfunction alpha(\n    TestClass12345 \$a,\n    TestClass12345 \$b,\n" .
                "    TestClass12345 \$c,\n    TestClass12345 \$d,\n    TestClass12345 \$e,\n" .
                "    TestClass12345 \$f,\n    TestClass12345 \$g\n) {\n}\n"
            ],
            ["<?php static \$a;", "<?php\nstatic \$a;\n"],
            [
                "<?php static \$a;function a(){global \$a;}",
                "<?php\nstatic \$a;\nfunction a()\n{\n    global \$a;\n}\n"
            ]
        ];
    }
}
