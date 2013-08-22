<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Page_Block_SwitchTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Test_Helper_ObjectManager
     */
    protected $_objectManager;

    protected function setUp()
    {
        $this->_objectManager = new Magento_Test_Helper_ObjectManager($this);
    }

    /**
     * @dataProvider testIsStoreInUrlDataProvider
     */
    public function testIsStoreInUrl($isUseStoreInUrl)
    {
        $storeMock = $this->getMock('Magento_Core_Model_Store', array(), array(), '', false);
        $storeMock->expects($this->once())->method('isUseStoreInUrl')->will($this->returnValue($isUseStoreInUrl));
        $storeManager = $this->getMock('Magento_Core_Model_StoreManagerInterface');
        $storeManager->expects($this->any())->method('getStore')->will($this->returnValue($storeMock));
        $block = $this->_objectManager->getObject('Magento_Page_Block_Switch', array('storeManager' => $storeManager));

        $this->assertEquals($block->isStoreInUrl(), $isUseStoreInUrl);

        // check cached value
        $this->assertEquals($block->isStoreInUrl(), $isUseStoreInUrl);
    }

    /**
     * @see self::testIsStoreInUrlDataProvider()
     * @return array
     */
    public function testIsStoreInUrlDataProvider()
    {
        return array(
            array(true),
            array(false),
        );
    }
}
