<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_Rma
 */
class Enterprise_Rma_Model_Item_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Rma_Model_Item_Form
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= new Enterprise_Rma_Model_Item_Form();
        $this->_model->setFormCode('default');
    }

    public function testGetAttributes()
    {
        $attributes = $this->_model->getAttributes();
        $this->assertInternalType('array', $attributes);
        $this->assertNotEmpty($attributes);
    }
}
