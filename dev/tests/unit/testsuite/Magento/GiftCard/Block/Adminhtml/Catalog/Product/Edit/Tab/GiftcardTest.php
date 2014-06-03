<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab;

class GiftcardTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getScopeValueDataProvider
     * @param boolean $isSingleStore
     * @param string $scope
     * @param string $expectedResult
     */
    public function testGetScopeValue($isSingleStore, $scope, $expectedResult)
    {

        $storeManagerMock = $this->getMockBuilder(
            'Magento\Store\Model\StoreManager'
        )->disableOriginalConstructor()->getMock();
        $storeManagerMock->expects(
            $this->any()
        )->method(
            'isSingleStoreMode'
        )->will(
            $this->returnValue($isSingleStore)
        );

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $block = $objectManagerHelper->getObject(
            'Magento\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Giftcard',
            array('storeManager' => $storeManagerMock)
        );


        $this->assertEquals($block->getScopeValue($scope), $expectedResult);
    }

    /**
     * @return array
     */
    public function getScopeValueDataProvider()
    {
        return array(array(true, 'test', ''), array(false, 'test', 'value-scope="test"'));
    }
}
