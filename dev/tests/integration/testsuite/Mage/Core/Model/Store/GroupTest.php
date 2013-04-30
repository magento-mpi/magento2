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
        $this->_model = Mage::getModel('Mage_Core_Model_Store_Group');
    }

    public function testSetGetWebsite()
    {
        $this->assertFalse($this->_model->getWebsite());
        $website = Mage::app()->getWebsite();
        $this->_model->setWebsite($website);
        $actualResult = $this->_model->getWebsite();
        $this->assertSame($website, $actualResult);
    }

    /**
     * @magentoConfigFixture limitations/store_group 1
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     * @expectedException Mage_Core_Exception
     * @expectedExceptionMessage You are using the maximum number of stores allowed.
     */
    public function testSaveValidationLimitation()
    {
        $this->_model->setData(
            array(
                'website_id'       => 0,
                'name'             => 'test store',
                'root_category_id' => 2,
                'default_store_id' => 0
            )
        );

        /* emulate admin store */
        Mage::app()->getStore()->setId(Mage_Core_Model_App::ADMIN_STORE_ID);
        $this->_model->save();
    }
}
