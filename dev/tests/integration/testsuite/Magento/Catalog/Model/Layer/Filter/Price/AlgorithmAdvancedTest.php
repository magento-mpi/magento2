<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Layer\Filter\Price;

/**
 * Test class for \Magento\Catalog\Model\Layer\Filter\Price.
 *
 * @magentoDataFixture Magento/Catalog/Model/Layer/Filter/Price/_files/products_advanced.php
 */
class AlgorithmAdvancedTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Algorithm model
     *
     * @var \Magento\Catalog\Model\Layer\Filter\Price\Algorithm
     */
    protected $_model;

    protected function setUp()
    {
         $this->_model = \Mage::getModel('Magento\Catalog\Model\Layer\Filter\Price\Algorithm');
    }

    /**
     * Prepare price filter model
     *
     * @param \Magento\TestFramework\Request|null $request
     */
    protected function _prepareFilter($request = null)
    {
        /** @var $layer \Magento\Catalog\Model\Layer */
        $layer = \Mage::getModel('Magento\Catalog\Model\Layer');
        $layer->setCurrentCategory(4);
        $layer->setState(\Mage::getModel('Magento\Catalog\Model\Layer\State'));
        /** @var $filter \Magento\Catalog\Model\Layer\Filter\Price */
        $filter = \Mage::getModel('Magento\Catalog\Model\Layer\Filter\Price');
        $filter->setLayer($layer)->setAttributeModel(new \Magento\Object(array('attribute_code' => 'price')));
        if (!is_null($request)) {
            $filter->apply($request, \Mage::app()->getLayout()->createBlock('Magento\Core\Block\Text'));
            $interval = $filter->getInterval();
            if ($interval) {
                $this->_model->setLimits($interval[0], $interval[1]);
            }
        }
        $collection = $layer->getProductCollection();
        $this->_model->setPricesModel($filter)->setStatistics(
            $collection->getMinPrice(),
            $collection->getMaxPrice(),
            $collection->getPriceStandardDeviation(),
            $collection->getSize()
        );
    }

    public function testWithoutLimits()
    {
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        $request->setParam('price', null);
        $this->_prepareFilter();
        $this->assertEquals(array(
            0 => array('from' => 0, 'to' => 20, 'count' => 3),
            1 => array('from' => 20, 'to' => '', 'count' => 4)
        ), $this->_model->calculateSeparators());
    }

    public function testWithLimits()
    {
        $this->markTestIncomplete('Bug MAGE-6561');
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $request \Magento\TestFramework\Request */
        $request = $objectManager->get('Magento\TestFramework\Request');
        $request->setParam('price', '10-100');
        $this->_prepareFilter($request);
        $this->assertEquals(array(
            0 => array('from' => 10, 'to' => 20, 'count' => 2),
            1 => array('from' => 20, 'to' => 100, 'count' => 2)
        ), $this->_model->calculateSeparators());
    }
}
