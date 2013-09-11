<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Rma_Model_Item_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Rma\Model\Item\Form
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model= Mage::getModel('Magento\Rma\Model\Item\Form');
        $this->_model->setFormCode('default');
    }

    public function testGetAttributes()
    {
        $attributes = $this->_model->getAttributes();
        $this->assertInternalType('array', $attributes);
        $this->assertNotEmpty($attributes);
    }
}
