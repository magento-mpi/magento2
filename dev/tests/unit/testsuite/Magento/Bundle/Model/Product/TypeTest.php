<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Bundle_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Bundle_Model_Product_Type
     */
    protected $_model;

    protected function setUp()
    {
        $objectHelper = new Magento_TestFramework_Helper_ObjectManager($this);
        $this->_model = $objectHelper->getObject('Magento_Bundle_Model_Product_Type', array(
            'productFactory' => $this->getMock('Magento_Catalog_Model_ProductFactory'),
            'bundleModelSelection' => $this->getMock('Magento_Bundle_Model_SelectionFactory'),
            'bundleFactory' => $this->getMock('Magento_Bundle_Model_Resource_BundleFactory'),
            'bundleCollection' => $this->getMock('Magento_Bundle_Model_Resource_Selection_CollectionFactory'),
            'bundleOption' => $this->getMock('Magento_Bundle_Model_OptionFactory'),
        ));
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but it should');
    }
}
