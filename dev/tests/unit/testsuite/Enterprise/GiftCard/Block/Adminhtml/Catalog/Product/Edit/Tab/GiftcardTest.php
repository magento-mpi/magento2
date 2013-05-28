<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftCard
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftCard_Block_Adminhtml_Catalog_Product_Edit_Tab_GiftcardTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getScopeValueDataProvider
     * @param boolean $isSingleStore
     * @param string $scope
     * @param string $expectedResult
     */
    public function testGetScopeValue($isSingleStore, $scope, $expectedResult)
    {
        $methods = array('getHelperFactory', 'getRequest', 'getLayout', 'getEventManager', 'getUrlBuilder',
            'getTranslator', 'getCache', 'getDesignPackage', 'getSession', 'getStoreConfig', 'getFrontController',
            'getDirs', 'getLogger', 'getFilesystem');
        $contextMock = $this->getMockBuilder('Mage_Core_Block_Template_Context')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();

        $helperMock = $this->getMockBuilder('Mage_Backend_Helper_Data')
            ->setMethods(array('__'))
            ->disableOriginalConstructor()
            ->getMock();
        $helperMock->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $helperFactoryMock = $this->getMock('Mage_Core_Model_Factory_Helper', array('get'));
        $helperFactoryMock->expects($this->any())
            ->method('get')
            ->with($this->equalTo('Mage_Backend_Helper_Data'))
            ->will($this->returnValue($helperMock));

        $contextMock->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactoryMock));

        $storeManagerMock = $this->getMockBuilder('Mage_Core_Model_StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('isSingleStoreMode'))
            ->getMock();
        $storeManagerMock->expects($this->any())
            ->method('isSingleStoreMode')
            ->will($this->returnValue($isSingleStore));

        $objectManagerHelper = new Magento_Test_Helper_ObjectManager($this);
        $block = $objectManagerHelper->getObject(
            'Enterprise_GiftCard_Block_Adminhtml_Catalog_Product_Edit_Tab_Giftcard',
            array('context' => $contextMock, 'storeManager' => $storeManagerMock)
        );


        $this->assertEquals($block->getScopeValue($scope), $expectedResult);
    }

    /**
     * @return array
     */
    public function getScopeValueDataProvider()
    {
        return array(
            array(true, 'test', ''),
            array(false, 'test', 'value-scope="test"'),
        );
    }
}
