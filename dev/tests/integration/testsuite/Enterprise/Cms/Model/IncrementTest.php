<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Cms
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @group module:Enterprise_Cms
 */
class Enterprise_Cms_Model_IncrementTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Enterprise_Cms_Model_Increment
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = new Enterprise_Cms_Model_Increment;
    }

    /**
     * Empty fixture that guarantee data isolation
     */
    public function emptyFixture()
    {}

    /**
     * @magentoDataFixture emptyFixture
     */
    public function testGetNewIncrementId()
    {
        $this->assertEmpty($this->_model->getId());
        $this->assertEmpty($this->_model->getIncrementType());
        $this->assertEmpty($this->_model->getIncrementNode());
        $this->assertEmpty($this->_model->getIncrementLevel());
        $this->_model->getNewIncrementId(Enterprise_Cms_Model_Increment::TYPE_PAGE, 1, 1);
        $this->assertEquals(Enterprise_Cms_Model_Increment::TYPE_PAGE, $this->_model->getIncrementType());
        $this->assertEquals(1, $this->_model->getIncrementNode());
        $this->assertEquals(1, $this->_model->getIncrementLevel());
        $this->assertNotEmpty($this->_model->getId());
    }
}
