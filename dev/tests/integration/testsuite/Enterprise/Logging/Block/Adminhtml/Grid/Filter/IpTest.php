<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Logging
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_Logging
 */
class Enterprise_Logging_Block_Adminhtml_Grid_Filter_IpTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Logging_Block_Adminhtml_Grid_Filter_Ip
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= new Enterprise_Logging_Block_Adminhtml_Grid_Filter_Ip();
    }

    public function testGetCondition()
    {
        $condition = $this->_model->getCondition();
        $this->assertArrayHasKey('field_expr', $condition);
        $this->assertArrayHasKey('like', $condition);
    }

    public function testGetConditionWithLike()
    {
        $this->_model->setValue('127');
        $condition = $this->_model->getCondition();
        $this->assertContains('127', (string) $condition['like']);
        $this->assertNotEquals('127', (string) $condition['like']); // DB-depended placeholder symbols were added
    }
}
