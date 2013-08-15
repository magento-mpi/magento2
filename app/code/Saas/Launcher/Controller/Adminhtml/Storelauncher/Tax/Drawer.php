<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax Drawer controller
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Controller_Adminhtml_Storelauncher_Tax_Drawer
    extends Saas_Launcher_Controller_BaseDrawer
{
    /**
     * Retrieve Drawer Content Action
     */
    public function loadAction()
    {
        $ruleModel = Mage::getModel('Mage_Tax_Model_Calculation_Rule');
        Mage::register('tax_rule', $ruleModel);
        parent::loadAction();
    }

    /**
     * Import Tax Rates
     */
    public function importTaxRatesAction()
    {
        $responseContent = '';
        try {
            /** @var $importHandler Mage_Tax_Model_Rate_CsvImportHandler */
            $importHandler = $this->_objectManager->create('Mage_Tax_Model_Rate_CsvImportHandler');
            $importHandler->importFromCsvFile($this->getRequest()->getFiles('import_rates_file'));

            Mage::register('tax_rule', Mage::getModel('Mage_Tax_Model_Calculation_Rule'));
            $this->loadLayout();
            /** @var $editRuleFormBlock Mage_Adminhtml_Block_Tax_Rule_Edit_Form */
            $editRuleFormBlock = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Tax_Rule_Edit_Form');
            $editRuleFormBlock->toHtml();
            $taxRateElement = $editRuleFormBlock->getForm()->getElement('tax_rate');
            /** @var $taxRatePopUpBlock Mage_Adminhtml_Block_Tax_Rate_Form */
            $taxRatePopUpBlock = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Tax_Rate_Form');

            $responseContent = Mage::helper('Saas_Launcher_Helper_Data')->jsonEncode(array(
                'success' => true,
                'error_message' => '',
                'tax_rate_field' => $taxRateElement->toHtml(),
                'tax_rate_collection' => $taxRatePopUpBlock->getRateCollection(),
            ));
        } catch (Exception $e) {
            $responseContent = Mage::helper('Saas_Launcher_Helper_Data')->jsonEncode(array(
                'success' => false,
                'error_message' => Mage::helper('Saas_Launcher_Helper_Data')->__($e->getMessage()),
                'tax_rate_field' => '',
                'tax_rate_collection' => '',
            ));
        }
        $this->getResponse()->setHeader('Content-Type', 'text/plain');
        $this->getResponse()->setBody($responseContent);
    }
}
