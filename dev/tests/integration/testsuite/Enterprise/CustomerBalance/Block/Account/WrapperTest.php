<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CustomerBalance
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_CustomerBalance_Block_Account_WrapperTest extends PHPUnit_Framework_TestCase
{
    /**
     * List of block injection classes
     *
     * @var array
     */
    protected $_blockInjections = array(
        'Mage_Core_Model_BlockFactory',
        'Magento_Data_Structure',
        'Mage_Core_Model_Layout_Argument_Processor',
        'Mage_Core_Model_Layout_Translator',
        'Mage_Core_Model_Layout_ScheduledStructure'
    );

    /**
     * @magentoConfigFixture modules/Enterprise_CustomerBalance/active 1
     * @magentoDataFixture Enterprise/CustomerBalance/_files/history.php
     */
    public function testToHtml()
    {
        $session = Mage::getModel('Mage_Customer_Model_Session');
        $session->login('customer@example.com', 'password');

        $utility = new Mage_Core_Utility_Layout($this);
        $layout = $utility->getLayoutFromFixture(__DIR__ . '/../../_files/account_wrapper.xml',
            $this->_prepareConstructorArguments()
        );
        $layout->getUpdate()->addHandle('enterprise_customerbalance_info_index')->load();
        $layout->generateXml()->generateElements();
        $layout->addOutputElement('customerbalance.wrapper');
        $html = $layout->getOutput();

        $this->assertContains('<div class="storecredit">', $html);
        $format = '%A<div class="account-balance">%A<table id="customerbalance-history" class="data-table">%A';
        $this->assertStringMatchesFormat($format, $html);
    }
    /**
     * List of block constructor arguments
     *
     * @return array
     */
    protected function _prepareConstructorArguments()
    {
        $arguments = array();
        foreach ($this->_blockInjections as $injectionClass) {
            $arguments[] = Mage::getModel($injectionClass);
        }
        return $arguments;
    }
}
