<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Config\Section\Reader;

class WebsiteTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Core\Model\Config\Section\Reader\Website
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_initialConfigMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_sectionPullMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_collectionFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_websiteMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_appStateMock;

    protected function setUp()
    {
        $this->_initialConfigMock = $this->getMock('Magento\Core\Model\Config\Initial', array(), array(), '', false);
        $this->_sectionPullMock = $this->getMock('Magento\Core\Model\Config\SectionPool', array(), array(), '', false);
        $this->_collectionFactory = $this->getMock(
            'Magento\Core\Model\Resource\Config\Value\Collection\ScopedFactory',
            array('create'),
            array(),
            '',
            false
        );
        $websiteFactoryMock = $this->getMock('Magento\Core\Model\WebsiteFactory', array('create'), array(), '', false);
        $this->_websiteMock = $this->getMock('Magento\Core\Model\Website', array(), array(), '', false);
        $websiteFactoryMock->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_websiteMock));

        $this->_appStateMock = $this->getMock('Magento\App\State', array(), array(), '', false);
        $this->_appStateMock->expects($this->any())
            ->method('isInstalled')
            ->will($this->returnValue(true));

        $this->_model = new \Magento\Core\Model\Config\Section\Reader\Website(
            $this->_initialConfigMock,
            $this->_sectionPullMock,
            new \Magento\Core\Model\Config\Section\Converter(),
            $this->_collectionFactory,
            $websiteFactoryMock,
            $this->_appStateMock
        );
    }

    public function testRead()
    {
        $websiteCode = 'default';
        $websiteId = 1;

        $sectionMock = $this->getMock('Magento\Core\Model\Config\Data', array(), array(), '', false);
        $sectionMock->expects($this->any())
            ->method('getValue')
            ->will($this->returnValue(array(
            'config' => array('key0' => 'default_value0', 'key1' => 'default_value1'),
        )));
        $sectionMock->expects($this->once())
            ->method('getSource')
            ->will($this->returnValue(array(
            'config' => array('key0' => 'default_value0', 'key1' => 'default_value1'),
        )));
        $this->_sectionPullMock->expects($this->once())
            ->method('getSection')
            ->with('default', null)
            ->will($this->returnValue($sectionMock));

        $this->_initialConfigMock->expects($this->any())
            ->method('getWebsite')
            ->with($websiteCode)
            ->will($this->returnValue(array(
                'config' => array('key1' => 'website_value1', 'key2' => 'website_value2'),
            )));
        $this->_websiteMock->expects($this->once())
            ->method('load')
            ->with($websiteCode);
        $this->_websiteMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($websiteId));
        $this->_collectionFactory->expects($this->once())
            ->method('create')
            ->with(array('scope' => 'websites', 'scopeId' => $websiteId))
            ->will($this->returnValue(array(
                new \Magento\Object(array('path' => 'config/key1', 'value' => 'website_db_value1')),
                new \Magento\Object(array('path' => 'config/key3', 'value' => 'website_db_value3')),
            )));
        $expectedData = array(
            'config' => array(
                'key0' => 'default_value0', // value from default section
                'key1' => 'website_db_value1',
                'key2' => 'website_value2', // value that has not been overridden in DB
                'key3' => 'website_db_value3'
            ),
        );
        $this->assertEquals($expectedData, $this->_model->read($websiteCode));
    }
}
