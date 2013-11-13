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
        $methods = array('getHelperFactory', 'getRequest', 'getLayout', 'getEventManager', 'getUrlBuilder',
            'getTranslator', 'getCache', 'getDesignPackage', 'getSession', 'getStoreConfig', 'getFrontController',
            'getDirs', 'getLogger', 'getFilesystem');
        $contextMock = $this->getMockBuilder('Magento\Backend\Block\Template\Context')
            ->disableOriginalConstructor()
            ->setMethods($methods)
            ->getMock();

        $helperFactoryMock = $this->getMock('Magento\Core\Model\Factory\Helper', array('get'), array(), '', false);

        $contextMock->expects($this->any())
            ->method('getHelperFactory')
            ->will($this->returnValue($helperFactoryMock));

        $storeManagerMock = $this->getMockBuilder('Magento\Core\Model\StoreManager')
            ->disableOriginalConstructor()
            ->setMethods(array('isSingleStoreMode'))
            ->getMock();
        $storeManagerMock->expects($this->any())
            ->method('isSingleStoreMode')
            ->will($this->returnValue($isSingleStore));

        $objectManagerHelper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $block = $objectManagerHelper->getObject(
            'Magento\GiftCard\Block\Adminhtml\Catalog\Product\Edit\Tab\Giftcard',
            array(
                'context' => $contextMock,
                'storeManager' => $storeManagerMock,
                'templateOptions' => $this->getMockBuilder('Magento\Backend\Model\Config\Source\Email\TemplateFactory')
                    ->getMock()
            )
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
