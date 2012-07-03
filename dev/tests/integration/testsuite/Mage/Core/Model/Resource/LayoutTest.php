<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Core_Model_Resource_LayoutTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Resource_Layout
     */
    protected $_model;

    public function setUp()
    {
        $this->_model = new Mage_Core_Model_Resource_Layout();
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    public function testFetchUpdatesByHandle()
    {
        $this->assertEmpty($this->_model->fetchUpdatesByHandle('test', array('test' => 'test')));
    }
}
