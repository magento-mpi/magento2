<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Search
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Enterprise_Search_Model_Adminhtml_System_Config_Source_EngineTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Search_Model_Adminhtml_System_Config_Source_Engine
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model= Mage::getModel('Enterprise_Search_Model_Adminhtml_System_Config_Source_Engine');
    }

    public function testToOptionArray()
    {
        $options = $this->_model->toOptionArray();
        $this->assertNotEmpty($options);

        foreach ($options as $option) {
            $this->assertArrayHasKey('label', $option);
            $this->assertArrayHasKey('value', $option);
            $this->assertTrue(class_exists($option['value']));
        }
    }
}
