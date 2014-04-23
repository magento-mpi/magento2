<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Data
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tests for \Magento\Framework\Data\Form\Element\Imagefile
 */
namespace Magento\Framework\Data\Form\Element;

class ImagefileTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_objectManagerMock;

    /**
     * @var \Magento\Framework\Data\Form\Element\Imagefile
     */
    protected $_imagefile;

    protected function setUp()
    {
        $factoryMock = $this->getMock('\Magento\Framework\Data\Form\Element\Factory', array(), array(), '', false);
        $collectionFactoryMock = $this->getMock(
            '\Magento\Framework\Data\Form\Element\CollectionFactory',
            array(),
            array(),
            '',
            false
        );
        $escaperMock = $this->getMock('\Magento\Escaper', array(), array(), '', false);
        $this->_imagefile = new \Magento\Framework\Data\Form\Element\Imagefile(
            $factoryMock,
            $collectionFactoryMock,
            $escaperMock
        );
    }

    /**
     * @covers \Magento\Framework\Data\Form\Element\Imagefile::__construct
     */
    public function testConstruct()
    {
        $this->assertEquals('file', $this->_imagefile->getType());
        $this->assertEquals('imagefile', $this->_imagefile->getExtType());
        $this->assertFalse($this->_imagefile->getAutosubmit());
        $this->assertFalse($this->_imagefile->getData('autoSubmit'));

    }
}
