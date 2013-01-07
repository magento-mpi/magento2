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

class Mage_Core_Model_Store_GroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Core_Model_Store_Group
     */
    protected $_model;

    public function setUp()
    {
        $eventDispatcher = Mage::getObjectManager()->get('Mage_Core_Model_Event_Manager');
        $cacheManager = Mage::getObjectManager()->get('Mage_Core_Model_Cache');
        $this->_model = new Mage_Core_Model_Store_Group($eventDispatcher, $cacheManager);
    }

    public function tearDown()
    {
        $this->_model = null;
    }

    public function testSetGetWebsite()
    {
        $this->assertFalse($this->_model->getWebsite());
        $website = Mage::app()->getWebsite();
        $this->_model->setWebsite($website);
        $actualResult = $this->_model->getWebsite();
        $this->assertEquals($website, $actualResult);
    }
}