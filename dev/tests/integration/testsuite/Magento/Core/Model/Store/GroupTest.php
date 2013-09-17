<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Core_Model_Store_GroupTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_Core_Model_Store_Group
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = Mage::getModel('Magento_Core_Model_Store_Group');
    }

    public function testSetGetWebsite()
    {
        $this->assertFalse($this->_model->getWebsite());
        $website = Mage::app()->getWebsite();
        $this->_model->setWebsite($website);
        $actualResult = $this->_model->getWebsite();
        $this->assertSame($website, $actualResult);
    }
}
