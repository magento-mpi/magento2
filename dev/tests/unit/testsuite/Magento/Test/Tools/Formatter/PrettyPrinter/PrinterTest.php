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
}
