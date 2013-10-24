<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Printer;

class PrinterTest extends TestBase
{
    /**
     * This method tests arrays in the pretty printer.
     *
     * @dataProvider dataArrays
     */
    public function testArrays($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    /**
     * Provide data to test method
     *
     * @return array
     */
    public function dataArrays()
    {
        return array(
            array(
                "<?php class A1 {public \$a = array();}",
                "<?php\nclass A1\n{\n    public \$a = array();\n}\n"
            ),
            array(
                "<?php class A2 {public \$a2 = array(1,2,3,4,5);}",
                "<?php\nclass A2\n{\n    public \$a2 = array(1, 2, 3, 4, 5);\n}\n"
            ),
            array(
                "<?php class A3 {public \$a3 = array(1=>'alpha',2=>'beta',3=>'gamma');}",
                "<?php\nclass A3\n{\n    public \$a3 = array(1 => 'alpha', 2 => 'beta', 3 => 'gamma');\n}\n"
            ),
            array(
                "<?php class A3 {public \$a3 = array(1=>'alpha1234567890',2=>'beta1234567890',3=>'gamma1234567890');}",
                "<?php\nclass A3\n{\n    public \$a3 = array(\n        1 => 'alpha1234567890',\n" .
                "        2 => 'beta1234567890',\n        3 => 'gamma1234567890'\n    );\n}\n"
            ),
            array(
                "<?php class A1 {public \$a = array(array(1.1,1.2),array(2.1,2.2));}",
                "<?php\nclass A1\n{\n    public \$a = array(array(1.1, 1.2), array(2.1, 2.2));\n}\n"
            ),
        );
    }

    /**
     * This method tests some of the basics of the pretty printer.
     *
     * @dataProvider dataProviderBasics
     */
    public function testBasics($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
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
        return array(
            array(<<<ORIGINALCODESNIPPET
<?php
/**
 * Class Foo
 */
class Foo extends Bar implements Zulu {
    /** alpha method */
    public function alpha() {
        return \$this;
    }
    /** beta method */
    public function beta() {
        return \$this->alpha()->alpha()->alpha()->alpha()->alpha()->alpha()->alpha()->alpha()->alpha()->alpha();
    }
}
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
<?php
/**
 * Class Foo
 */
class Foo extends Bar implements Zulu
{
    /** alpha method */
    public function alpha()
    {
        return \$this;
    }

    /** beta method */
    public function beta()
    {
        return \$this->alpha()
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

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
  namespace Magento\\Test;
class Foo {
    function x() {
        \$d = 1+1;

    }
};
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
<?php
namespace Magento\\Test;

class Foo
{
    public function x()
    {
        \$d = 1 + 1;
    }
}

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
  namespace Magento\\Test;
class Foo {
    function x() {
        unset(\$d);

    }
};
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
<?php
namespace Magento\\Test;

class Foo
{
    public function x()
    {
        unset(\$d);
    }
}

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
  namespace Magento\\Test;
class Foo {
    function x() {
        throw new \Exception('error');

    }
};
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
<?php
namespace Magento\\Test;

class Foo
{
    public function x()
    {
        throw new \Exception('error');
    }
}

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Errors
 * @copyright   {copyright}
 * @license     {license_link}
 */

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

\$processor = 1+2;


ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Errors
 * @copyright   {copyright}
 * @license     {license_link}
 */

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
\$processor = 1 + 2;

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
  namespace Magento\\Test;
class Foo {
    function x() {
        \$d=1+1;
        echo 1 + (2 - 2) * 3;

    }
};
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
<?php
namespace Magento\\Test;

class Foo
{
    public function x()
    {
        \$d = 1 + 1;
        echo 1 + (2 - 2) * 3;
    }
}

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
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
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
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

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
  namespace Magento\\Test;


class Foo {
    function x() {
        echo 'y';

        echo 'z';
    }
};
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
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

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
  namespace Magento\\Test;
class Foo {
    function x() {
        echo 'y';

        echo 'z';

    }
};
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
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

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
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
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
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

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
  namespace Magento\\Test;
class Foo {
    function x() {
        // Comment at start of block

        // echo 'b';

        echo 'y' . 'e' . 's';
        echo 'somehing\\n'.(1+2).'more';

        echo 'z';
        echo "My super string \\n Is here".(1).'mixed'.\$v;
        // Comment at end of block
    }
};
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
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
        echo "My super string \\n Is here" . 1 . 'mixed' . \$v;
    }
}

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
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
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
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

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
  namespace Magento\\Test;
class Foo {
    protected function _construct() {
        \$this->_testPropertyCall = 'test_text';
        \$this->_testFunctionCall = __('Testing');
    }
};
ORIGINALCODESNIPPET
            , <<<FORMATTEDCODESNIPPET
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

FORMATTEDCODESNIPPET
            )
        );
    }

    /**
     * This method tests the printing around classes.
     *
     * @dataProvider dataProviderClassDeclaration
     */
    public function testClassDeclaration($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    public function dataProviderClassDeclaration()
    {
        return array(
            array("<?php class NoChildren {}", "<?php\nclass NoChildren\n{\n}\n"),
            array("<?php class JustParent extends Parent {}", "<?php\nclass JustParent extends Parent\n{\n}\n"),
            array(
                "<?php class JustParent extends Fully\\Qualified\\Parent {}",
                "<?php\nclass JustParent extends Fully\\Qualified\\Parent\n{\n}\n"
            ),
            array(
                "<?php class JustParent extends \\Fully\\Qualified\\Parent {}",
                "<?php\nclass JustParent extends \\Fully\\Qualified\\Parent\n{\n}\n"
            ),
            array(
                "<?php class JustParent extends \\Fully\\Qualified\\Parent implements ".
                "\\Fully\\Qualified\\HelperInterface {}",
                "<?php\nclass JustParent extends \\Fully\\Qualified\\Parent implements\n".
                "    \\Fully\\Qualified\\HelperInterface\n{\n}\n"
            ),
            array(
                "<?php class JustParent implements \\Fully\\Qualified\\HelperInterface {}",
                "<?php\nclass JustParent implements \\Fully\\Qualified\\HelperInterface\n{\n}\n"
            ),
            array(
                "<?php class JustInterface implements Interface1 {}",
                "<?php\nclass JustInterface implements Interface1\n{\n}\n"
            ),
            array(
                "<?php class JustInterfaces implements Interface1, Interface2, Interface3 {}",
                "<?php\nclass JustInterfaces implements Interface1, Interface2, Interface3\n{\n}\n"
            ),
            array(
                "<?php class ParentPlus extends Parent implements Interface1 {}",
                "<?php\nclass ParentPlus extends Parent implements Interface1\n{\n}\n"
            ),
            array(
                "<?php class ParentPluses extends Parent implements Interface1, Interface2, Interface3 {}",
                "<?php\nclass ParentPluses extends Parent implements Interface1, Interface2, Interface3\n{\n}\n"
            ),
            array(
                "<?php class BigParentPluses extends Parent implements Interface1, Interface2, Interface3, " .
                "Interface4 {}",
                "<?php\nclass BigParentPluses extends Parent implements\n    Interface1,\n    Interface2,\n" .
                "    Interface3,\n    Interface4\n{\n}\n"
            ),
            array("<?php interface NoChildren {}", "<?php\ninterface NoChildren\n{\n}\n"),
            array("<?php interface JustParent extends Parent {}", "<?php\ninterface JustParent extends Parent\n{\n}\n"),
            array(
                "<?php interface BigParent extends Interface1,Interface2,Interface3,Interface4,Interface5 {}",
                "<?php\ninterface BigParent extends\n    Interface1,\n    Interface2,\n    Interface3,\n" .
                "    Interface4,\n    Interface5\n{\n}\n"
            ),
        );
    }

    /**
     * This method tests the printing around namespace and use statements.
     *
     * @dataProvider dataProviderNamespace
     */
    public function testNamespace($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    public function dataProviderNamespace()
    {
        return array(
            array(
                "<?php /** filedoco */namespace LocalNamespace; class LocalClass {}",
                "<?php\n/** filedoco */\nnamespace LocalNamespace;\n\nclass LocalClass\n{\n}\n"
            ),
            array(
                "<?php /** filedoco */namespace LocalNs; use SomethingElse; class LocalC2 {}",
                "<?php\n/** filedoco */\nnamespace LocalNs;\n\nuse SomethingElse;\n\nclass LocalC2\n{\n}\n"
            ),
            array(
                "<?php /** filedoco */namespace LocalNs2; use WantingAlias as WAlias; class LocalC3 {}",
                "<?php\n/** filedoco */\nnamespace LocalNs2;\n\nuse WantingAlias as WAlias;\n\nclass LocalC3\n{\n}\n"
            ),
            array(
                "<?php /** filedoco */namespace LocalNs2; use SE1, SE2, SE3; class LocalC4 {}",
                "<?php\n/** filedoco */\nnamespace LocalNs2;\n\nuse SE1;\nuse SE2;\nuse SE3;\n\nclass LocalC4\n{\n}\n"
            ),
        );
    }

    /**
     * This method tests the printing around properties of a class.
     *
     * @dataProvider dataProviderProperties
     */
    public function testProperties($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    public function dataProviderProperties()
    {
        return array(
            array(
                "<?php class LocalClass {/** const doco */ const PI=3.14; /** member doc */ private \$foo='bar';}",
                "<?php\nclass LocalClass\n{\n    /** const doco */\n    const PI = 3.14;\n\n    /** member doc */\n" .
                "    private \$foo = 'bar';\n}\n"
            ),
            array(
                "<?php class Local2 {/** const doco */ const PI=3.14,e=2.71828; /** const2 doco */ const mu=1;}",
                "<?php\nclass Local2\n{\n    /** const doco */\n    const PI = 3.14, e = 2.71828;\n\n" .
                "    /** const2 doco */\n    const mu = 1;\n}\n"
            ),
            array(
                "<?php class Local3 {public static \$a;protected \$b;private \$c;public \$d,\$e,\$f;}",
                "<?php\nclass Local3\n{\n    public static \$a;\n\n    protected \$b;\n\n    private \$c;\n\n" .
                "    public \$d, \$e, \$f;\n}\n"
            ),
        );
    }

    /**
     * This method tests the printing around method declarations.
     *
     * @dataProvider dataMethodDeclarations
     */
    public function testMethodDeclarations($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    public function dataMethodDeclarations()
    {
        return array(
            array(
                "<?php class MD {public function alpha() {}}",
                "<?php\nclass MD\n{\n    public function alpha()\n    {\n    }\n}\n"
            ),
            array(
                "<?php class MD2 {public function alpha(\$a) {}}",
                "<?php\nclass MD2\n{\n    public function alpha(\$a)\n    {\n    }\n}\n"
            ),
            array(
                "<?php class MD3 {public function alpha(TestClass \$a) {}}",
                "<?php\nclass MD3\n{\n    public function alpha(TestClass \$a)\n    {\n    }\n}\n"
            ),
            array(
                "<?php class MD4 {public function alpha(TestClass \$a,TestClass \$b) {}}",
                "<?php\nclass MD4\n{\n    public function alpha(TestClass \$a, TestClass \$b)\n    {\n    }\n}\n"
            ),
            array(
                "<?php class MD5 {public function alpha(TestClass \$a,TestClass \$b,TestClass \$c,TestClass \$d) {}}",
                "<?php\nclass MD5\n{\n    public function alpha(\n        TestClass \$a,\n        TestClass \$b,\n" .
                "        TestClass \$c,\n        TestClass \$d\n    ) {\n    }\n}\n"
            ),
        );
    }

    /**
     * This method tests the printing around function declarations.
     *
     * @dataProvider dataFunctionDeclarations
     */
    public function testFunctionDeclarations($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    public function dataFunctionDeclarations()
    {
        return array(
            array(
                "<?php function alpha() {}",
                "<?php\nfunction alpha()\n{\n}\n"
            ),
            array(
                "<?php function alpha(\$a) {}",
                "<?php\nfunction alpha(\$a)\n{\n}\n"
            ),
            array(
                "<?php function alpha(TestClass \$a) {}",
                "<?php\nfunction alpha(TestClass \$a)\n{\n}\n"
            ),
            array(
                "<?php function alpha(TestClass \$a,TestClass \$b) {}",
                "<?php\nfunction alpha(TestClass \$a, TestClass \$b)\n{\n}\n"
            ),
            array(
                "<?php function alpha(TestClass12345 \$a,TestClass12345 \$b,TestClass12345 \$c,TestClass12345 \$d) {}",
                "<?php\nfunction alpha(\n    TestClass12345 \$a,\n    TestClass12345 \$b,\n" .
                "    TestClass12345 \$c,\n    TestClass12345 \$d\n) {\n}\n"
            ),
        );
    }
}
