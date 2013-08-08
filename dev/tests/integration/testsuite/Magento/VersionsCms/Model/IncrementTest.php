<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_VersionsCms_Model_IncrementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_VersionsCms_Model_Increment
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_VersionsCms_Model_Increment');
    }

    /**
     * @magentoDbIsolation enabled
     */
    public function testGetNewIncrementId()
    {
        $this->assertEmpty($this->_model->getId());
        $this->assertEmpty($this->_model->getIncrementType());
        $this->assertEmpty($this->_model->getIncrementNode());
        $this->assertEmpty($this->_model->getIncrementLevel());
        $this->_model->getNewIncrementId(Magento_VersionsCms_Model_Increment::TYPE_PAGE, 1, 1);
        $this->assertEquals(Magento_VersionsCms_Model_Increment::TYPE_PAGE, $this->_model->getIncrementType());
        $this->assertEquals(1, $this->_model->getIncrementNode());
        $this->assertEquals(1, $this->_model->getIncrementLevel());
        $this->assertNotEmpty($this->_model->getId());
    }
}
