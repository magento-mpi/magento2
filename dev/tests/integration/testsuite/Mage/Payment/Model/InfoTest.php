<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Payment
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Payment_Model_InfoTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_Payment_Model_Info
     */
    protected $_model;

    /**
     * @var Mage_Core_Helper_Data
     */
    protected $_helper;

    protected function setUp()
    {
        $this->_helper = Mage::helper('Mage_Core_Helper_Data');
        $this->_model = new Mage_Payment_Model_Info();
    }

    protected function tearDown()
    {
        unset($this->_model);
        unset($this->_helper);
    }

    /**
     * @param string $getMethod getter method name
     * @param string $setMethod setter method name
     * @param string $fieldName field name
     *
     * @dataProvider getSetFieldsDataProvider
     */
    public function testGetSetEncryptedFields($getMethod, $setMethod, $fieldName)
    {
        $this->_model->setData('method', 'ccsave');

        $actual = 'test-data';

        $this->_model->$setMethod($actual);

        /**
         * Check that data was encrypted before set
         */
        $this->assertEquals($actual, $this->_helper->decrypt($this->_model->getData($fieldName)));

        /**
         * Check that data was encrypted before returning in getters
         */
        $this->assertEquals($actual, $this->_model->$getMethod());
    }

    /**
     * @param string $getMethod getter method name
     * @param string $setMethod setter method name
     * @param string $fieldName field name
     *
     * @dataProvider getSetFieldsDataProvider
     */
    public function testGetSetNotEncryptedFields($getMethod, $setMethod, $fieldName)
    {
        $this->_model->setData('method', 'some_other_method');

        $actual = 'test-data';

        $this->_model->$setMethod($actual);

        /**
         * Check that data was not modified before set
         */
        $this->assertEquals($actual, $this->_model->getData($fieldName));

        /**
         * Check that data was not modified before returning in getters
         */
        $this->assertEquals($actual, $this->_model->$getMethod());
    }

    /**
     * @return array
     */
    public function getSetFieldsDataProvider()
    {
        return array(
            'cc_owner' => array('getCcOwner', 'setCcOwner', 'cc_owner'),
            'cc_exp_month' => array('getCcExpMonth', 'setCcExpMonth', 'cc_exp_month'),
            'cc_exp_year' => array('getCcExpYear', 'setCcExpYear', 'cc_exp_year'),
        );
    }
}
