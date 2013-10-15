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
     * This method tests some of the basics of the pretty printer.
     *
     * @dataProvider dataProviderBasics
     */
    public function testBasics($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    public function dataProviderBasics()
    {
        return array(
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
}

FORMATTEDCODESNIPPET
            ),
            array(<<<ORIGINALCODESNIPPET
<?php
/**
 * Class Foo
 */
class Foo extends Bar implements Zulu {
    public function alpha() {
        return \$this;
    }

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
}
