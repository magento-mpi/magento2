<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Test\Tools\Formatter\PrettyPrinter;

use Magento\Tools\Formatter\PrettyPrinter\Printer;

class PrinterReferenceTest extends TestBase
{
    /**
     * This method tests for various reference elements.
     *
     * @dataProvider dataReferences
     */
    public function testReferences($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
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
        );
    }

}