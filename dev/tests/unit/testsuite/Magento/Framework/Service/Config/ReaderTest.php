<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\Service\Config;

class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Service\Config\Reader
     */
    protected $_reader;

    /**
     * Prepare parameters
     */
    public function setUp()
    {
        $fileResolver = $this->getMockBuilder('Magento\Framework\App\Config\FileResolver')
            ->disableOriginalConstructor()
            ->getMock();
        $converter = $this->getMockBuilder('Magento\Framework\Service\Config\Converter')
            ->disableOriginalConstructor()
            ->getMock();
        $schema = $this->getMockBuilder('Magento\Framework\Service\Config\SchemaLocator')
            ->disableOriginalConstructor()
            ->getMock();
        $validator = $this->getMockBuilder('\Magento\Framework\Config\ValidationStateInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $this->_reader = new \Magento\Framework\Service\Config\Reader($fileResolver, $converter, $schema, $validator);
    }

    /**
     * Test creating object
     */
    public function testInstanceof()
    {
        $this->assertInstanceOf('Magento\Framework\Service\Config\Reader', $this->_reader);
    }
}
