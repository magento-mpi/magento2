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
        $filesystem = $this->getMockBuilder('Magento_Filesystem')->disableOriginalConstructor()->getMock();
        $coreRegistry = $this->getMock('Magento_Core_Model_Registry', array(), array(), '', false);
        $this->_model = new Magento_Bundle_Model_Product_Type($filesystem, $coreRegistry);
    }

    public function testHasWeightTrue()
    {
        $this->assertTrue($this->_model->hasWeight(), 'This product has not weight, but it should');
    }
}
