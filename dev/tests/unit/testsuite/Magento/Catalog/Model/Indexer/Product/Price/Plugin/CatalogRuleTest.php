<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Price\Plugin;


class CatalogRuleTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\TestFramework\Helper\ObjectManager
     */
    protected $_objectManager;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Plugin\CatalogRule
     */
    protected $_model;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $_priceProcessorMock;

    protected function setUp()
    {
        $this->_objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);

        $this->_priceProcessorMock = $this->getMock(
            'Magento\Catalog\Model\Indexer\Product\Price\Processor', array(), array(), '', false
        );

        $this->_model = $this->_objectManager->getObject(
            '\Magento\Catalog\Model\Indexer\Product\Price\Plugin\CatalogRule',
            array(
                'processor' => $this->_priceProcessorMock
            )
        );
    }

    public function testAfterApplyAll()
    {
        $this->_priceProcessorMock->expects($this->once())
            ->method('markIndexerAsInvalid');

        $ruleMock = $this->getMock('Magento\CatalogRule\Model\Rule', array(), array(), '', false);
        $this->_model->afterApplyAll($ruleMock);
    }

    /**
     * @param int|\Magento\Catalog\Model\Product $product
     * @param int $expectedIdCall
     * @dataProvider affectedProductsDataProvider
     */
    public function testAroundApplyToProduct($product, $expectedIdCall)
    {
        $this->_priceProcessorMock->expects($this->once())
            ->method('reindexRow')
            ->with($expectedIdCall);

        $ruleMock = $this->getMock('Magento\CatalogRule\Model\Rule', array(), array(), '', false);
        $this->_model->aroundApplyToProduct(
            $ruleMock,
            function () {

            },
            $product
        );
    }

    /**
     * @param int|\Magento\Catalog\Model\Product $product
     * @param int $expectedIdCall
     * @dataProvider affectedProductsDataProvider
     */
    public function testAroundApplyAllRulesToProduct($product, $expectedIdCall)
    {
        $this->_priceProcessorMock->expects($this->once())
            ->method('reindexRow')
            ->with($expectedIdCall);

        $ruleMock = $this->getMock('Magento\CatalogRule\Model\Rule', array(), array(), '', false);
        $this->_model->aroundApplyToProduct(
            $ruleMock,
            function () {

            },
            $product
        );
    }

    /**
     * @return array
     */
    public function affectedProductsDataProvider()
    {
        $productId = 11;
        $productMock = $this->getMock('Magento\Catalog\Model\Product', array(), array(), '', false);
        $productMock->expects($this->any())
            ->method('getId')
            ->will($this->returnValue($productId));

        return array(
            array($productId, $productId),
            array($productMock, $productId)
        );
    }
}
