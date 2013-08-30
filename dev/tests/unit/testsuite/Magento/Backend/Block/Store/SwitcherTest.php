<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Backend_Block_Store_SwitcherTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Backend_Block_Store_Switcher
     */
    protected $_object;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_applicationModel;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_websiteFactory;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_storeGroupFactory;

    protected function setUp()
    {
        $this->_applicationModel = $this->getMock('Magento_Core_Model_App', array(), array(), '', false);
        $this->_websiteFactory = $this->getMock('Magento_Core_Model_Website_Factory', array(), array(), '', false);
        $this->_storeGroupFactory = $this->getMock(
            'Magento_Core_Model_Store_Group_Factory',
            array(),
            array(),
            '',
            false
        );

        $helper = new Magento_Test_Helper_ObjectManager($this);
        $this->_object = $helper->getObject('Magento_Backend_Block_Store_Switcher', array(
            'urlBuilder' => $this->getMock('Magento_Backend_Model_Url', array(), array(), '', false),
            'application' => $this->_applicationModel,
            'websiteFactory' => $this->_websiteFactory,
            'storeGroupFactory' => $this->_storeGroupFactory
        ));
    }

    /**
     * @covers Magento_Backend_Block_Store_Switcher::getWebsiteCollection
     */
    public function testGetWebsiteCollectionWhenWebSiteIdsEmpty()
    {
        $websiteModel = $this->getMock('Magento_Core_Model_Website', array(), array(), '', false, false);
        $collection = $this->getMock(
            'Magento_Core_Model_Resource_Website_Collection',
            array(),
            array(),
            '',
            false,
            false
        );
        $websiteModel->expects($this->once())->method('getResourceCollection')->will($this->returnValue($collection));

        $expected = array('test', 'data', 'some');
        $collection->expects($this->once())->method('load')->will($this->returnValue($expected));
        $collection->expects($this->never())->method('addIdFilter');

        $this->_websiteFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($websiteModel));

        $this->_object->setWebsiteIds(null);

        $actual = $this->_object->getWebsiteCollection();
        $this->assertEquals($expected, $actual);
    }


    /**
     * @covers Magento_Backend_Block_Store_Switcher::getWebsiteCollection
     */
    public function testGetWebsiteCollectionWhenWebSiteIdsIsSet()
    {
        $websiteModel = $this->getMock('Magento_Core_Model_Website', array(), array(), '', false, false);
        $collection = $this->getMock(
            'Magento_Core_Model_Resource_Website_Collection',
            array(),
            array(),
            '',
            false,
            false
        );
        $websiteModel->expects($this->once())->method('getResourceCollection')->will($this->returnValue($collection));

        $ids = array(1, 2, 3);
        $this->_object->setWebsiteIds($ids);

        $expected = array('test', 'data', 'some');
        $collection->expects($this->once())->method('load')->will($this->returnValue($expected));
        $collection->expects($this->once())->method('addIdFilter')->with($ids);

        $this->_websiteFactory->expects($this->once())
            ->method('create')
            ->will($this->returnValue($websiteModel));

        $actual = $this->_object->getWebsiteCollection();
        $this->assertEquals($expected, $actual);
    }

    /**
     * @covers Magento_Backend_Block_Store_Switcher::getWebsites
     */
    public function testGetWebsitesWhenWebSiteIdsIsNotSet()
    {
        $this->_object->setWebsiteIds(null);

        $expected = array('test', 'data', 'some');
        $this->_applicationModel->expects($this->once())->method('getWebsites')->will($this->returnValue($expected));

        $this->assertEquals($expected, $this->_object->getWebsites());
    }

    /**
     * @covers Magento_Backend_Block_Store_Switcher::getWebsites
     */
    public function testGetWebsitesWhenWebSiteIdsIsSetAndMatchWebsites()
    {
        $ids = array(1, 3, 5);
        $webSites = array(
            1 => 'site 1',
            2 => 'site 2',
            3 => 'site 3',
            4 => 'site 4',
            5 => 'site 5',
        );

        $this->_object->setWebsiteIds($ids);

        $expected = array(
            1 => 'site 1',
            3 => 'site 3',
            5 => 'site 5',
        );
        $this->_applicationModel->expects($this->once())->method('getWebsites')->will($this->returnValue($webSites));

        $this->assertEquals($expected, $this->_object->getWebsites());
    }

    /**
     * @covers Magento_Backend_Block_Store_Switcher::getWebsites
     */
    public function testGetWebsitesWhenWebSiteIdsIsSetAndNotMatchWebsites()
    {
        $ids = array(8, 10, 12);
        $webSites = array(
            1 => 'site 1',
            2 => 'site 2',
            3 => 'site 3',
            4 => 'site 4',
            5 => 'site 5',
        );

        $this->_object->setWebsiteIds($ids);

        $expected = array();
        $this->_applicationModel->expects($this->once())->method('getWebsites')->will($this->returnValue($webSites));

        $this->assertEquals($expected, $this->_object->getWebsites());
    }
}
