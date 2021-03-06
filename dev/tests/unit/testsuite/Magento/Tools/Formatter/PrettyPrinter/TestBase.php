<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Tools\Formatter\PrettyPrinter;

require_once __DIR__ . '/../../../../../../../tools/PHP-Parser/lib/bootstrap.php';

/**
 * This class is used as a base class for the other tests in this package. Its main job is to include reference to the
 * bootstrap file.
 *
 * Class TestBase
 */
abstract class TestBase extends \PHPUnit_Framework_TestCase
{
    /**
     * This method converts the original code and checks to makes sure the result is equal to the
     * passed in formatted code.
     * @param string $originalCode String containing original code.
     * @param string $formattedCode String containing expected formatted code.
     */
    protected function convertAndCheck($originalCode, $formattedCode)
    {
        $printer = new Printer($originalCode);
        ob_start();
        $printer->parseCode();
        // disable printer output
        ob_end_clean();
        $this->assertEquals($formattedCode, $printer->getFormattedCode());
    }
}
