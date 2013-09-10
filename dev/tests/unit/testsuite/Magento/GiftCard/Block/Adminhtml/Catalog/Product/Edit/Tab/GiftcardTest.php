<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftCard
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_GiftCard_Block_Adminhtml_Catalog_Product_Edit_Tab_GiftcardTest extends PHPUnit_Framework_TestCase
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
        $contextMock = $this->getMockBuilder('Magento_Backend_Block_Template_Context')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();

        $helperFactoryMock = $this->getMock('Magento_Core_Model_Factory_Helper', array('get'), array(), '', false);

        $contextMock->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactoryMock));

        $storeManagerMock = $this->getMockBuilder('Magento_Core_Model_StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('isSingleStoreMode'))
            ->getMock();
        $storeManagerMock->expects($this->any())
            ->method('isSingleStoreMode')
            ->will($this->returnValue($isSingleStore));

        $objectManagerHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $block = $objectManagerHelper->getObject(
            'Magento_GiftCard_Block_Adminhtml_Catalog_Product_Edit_Tab_Giftcard',
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
