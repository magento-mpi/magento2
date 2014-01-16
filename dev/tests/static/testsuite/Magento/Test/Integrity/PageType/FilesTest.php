<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integrity_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Test\Integrity\PageType;

class FilesTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var string - xsd that will validate page_types.xml against
     */
    protected $_schemaFile;

    protected function setUp()
    {
        $this->_schemaFile = \Magento\TestFramework\Utility\Files::init()->getModuleFile(
            'Magento', 'Core', 'etc/page_types.xsd'
        );
    }

    public function testPageType()
    {
        $invoker = new \Magento\TestFramework\Utility\AggregateInvoker($this);
        $invoker(
            function ($pageType) {
                $dom = new \DOMDocument();
                $dom->loadXML(file_get_contents($pageType));
                $errors = \Magento\TestFramework\Utility\Validator::validateXml($dom, $this->_schemaFile);
                $this->assertTrue(empty($errors), print_r($errors, true));
            },
            \Magento\TestFramework\Utility\Files::init()->getPageTypeFiles()
        );
    }
}
