<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Printer;

class PrinterTest extends TestBase {
    /**
     * This method tests some of the basics of the pretty printer.
     *
     * @dataProvider dataProviderBasics
     */
    public function testBasics($originalCode, $formattedCode) {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }

    public function dataProviderBasics() {
        return array(
            array(<<<ORIGINALCODESNIPPET
<?php
class Foo {
}
ORIGINALCODESNIPPET
                , <<<FORMATTEDCODESNIPPET
<?php
class Foo
{
}
FORMATTEDCODESNIPPET
                )
        );
    }
}