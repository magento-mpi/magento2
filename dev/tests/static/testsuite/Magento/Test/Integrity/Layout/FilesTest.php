<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\Layout;

class FilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string
     */
    protected $_schemaFile;

    protected function setUp()
    {
        $this->_schemaFile = \Magento\TestFramework\Utility\Files::init()->getModuleFile(
            'Magento', 'Core', 'etc/layout_single.xsd'
        );
    }

    public function testLayouts()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            function ($layout) {
                $dom = new \DOMDocument();
                $dom->loadXML(file_get_contents($layout));
                $errors = \Magento\TestFramework\Utility\Validator::validateXml($dom, $this->_schemaFile);
                $this->assertTrue(empty($errors), print_r($errors, true));
            },
            \Magento\TestFramework\Utility\Files::init()->getLayoutFiles()
        );
    }
}
