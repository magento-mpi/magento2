<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Cron\Model\Config\Reader;

class XmlTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Cron\Model\Config\Reader\Xml
     */
    protected $_xmlReader;

    /**
     * Prepare parameters
     */
    public function setUp()
    {
        $fileResolver = $this->getMockBuilder('Magento\Core\Model\Config\FileResolver')
            ->disableOriginalConstructor()
            ->getMock();
        $converter = $this->getMockBuilder('Magento\Cron\Model\Config\Converter\Xml')
            ->disableOriginalConstructor()
            ->getMock();
        $schema = $this->getMockBuilder('Magento\Cron\Model\Config\SchemaLocator')
            ->disableOriginalConstructor()
            ->getMock();
        $validator = $this->getMockBuilder('Magento\Core\Model\Config\ValidationState')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_xmlReader = new \Magento\Cron\Model\Config\Reader\Xml($fileResolver, $converter, $schema, $validator);
    }

    /**
     * Test creating object
     */
    public function testInstanceof()
    {
        $this->assertInstanceOf('Magento\Cron\Model\Config\Reader\Xml', $this->_xmlReader);
    }
}
