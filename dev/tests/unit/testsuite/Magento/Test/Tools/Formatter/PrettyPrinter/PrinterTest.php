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

        // echo 'b';

        echo 'y';

        echo 'z';
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
}
