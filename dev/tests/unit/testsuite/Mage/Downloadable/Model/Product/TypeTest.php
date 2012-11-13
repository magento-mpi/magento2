<?php
    /**
     * {license_notice}
     *
     * @category    Magento
     * @package     Mage_Downloadable
     * @subpackage  unit_tests
     * @copyright   {copyright}
     * @license     {license_link}
     */

class Mage_Downloadable_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Downloadable_Model_Product_Type
     */
    protected $_model;

    protected function setUp()
    {
        $this-> _model = new Mage_Downloadable_Model_Product_Type();
    }

    public function testHasWeightFalse()
    {
        $this->assertFalse($this->_model->hasWeight(), 'This product has weight, but it should not');
    }
}
