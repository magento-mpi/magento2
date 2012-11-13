<?php
    /**
     * {license_notice}
     *
     * @category    Magento
     * @package     Mage_Bundle
     * @subpackage  unit_tests
     * @copyright   {copyright}
     * @license     {license_link}
     */

class Mage_Bundle_Model_Product_TypeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Bundle_Model_Product_Type
     */
    protected $_model;

    protected function setUp()
    {
        $this-> _model = new Mage_Bundle_Model_Product_Type();
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but should');
    }
}
