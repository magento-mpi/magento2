<?php
/**
 * {license_notice}
 *
 * @category    tests
 * @package     static
 * @subpackage  Legacy
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests usage of \Magento\View\Element\AbstractBlock
 */
namespace Magento\Test\Legacy\Magento\Core\Block;

class AbstractBlockTest extends \PHPUnit_Framework_TestCase
{
    public function testGetChildHtml()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            function ($file) {
                $result = \Magento\TestFramework\Utility\Classes::getAllMatches(
                    file_get_contents($file),
                    "/(->getChildHtml\([^,()]+, ?[^,()]+,)/i"
                );
                $this->assertEmpty(
                    $result,
                    "3rd parameter is not needed anymore for getChildHtml() in '{$file}': " . print_r($result, true)
                );
                $result = \Magento\TestFramework\Utility\Classes::getAllMatches(
                    file_get_contents($file),
                    "/(->getChildChildHtml\([^,()]+, ?[^,()]+, ?[^,()]+,)/i"
                );
                $this->assertEmpty(
                    $result,
                    "4th parameter is not needed anymore for getChildChildHtml() in '{$file}': " . print_r(
                        $result,
                        true
                    )
                );
            },
            \Magento\TestFramework\Utility\Files::init()->getPhpFiles()
        );
    }
}
