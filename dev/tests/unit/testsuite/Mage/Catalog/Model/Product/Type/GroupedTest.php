<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Model_Product_Type_GroupedTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Catalog_Model_Product_Type_Grouped
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Mage_Catalog_Model_Product_Type_Grouped();
    }

    public function testHasWeightFalse()
    {
        $this->assertFalse($this->_model->hasWeight(), 'This product has weight, but it should not');
    }
}