<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax Drawer controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_Storelauncher_Tax_DrawerController
    extends Mage_Launcher_Controller_BaseDrawer
{
    /**
     * Tax Drawer Block Class Name
     *
     * @var string
     */
    protected $_drawerBlockName = 'Mage_Launcher_Block_Adminhtml_Storelauncher_Tax_Drawer';

    /**
     * Tax Tile Block Class Name
     *
     * @var string
     */
    protected $_tileBlockName = 'Mage_Launcher_Block_Adminhtml_Storelauncher_Tax_Tile';

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
            $this->_importRates();

            Mage::register('tax_rule', Mage::getModel('Mage_Tax_Model_Calculation_Rule'));
            $this->loadLayout();
            /** @var $editRuleFormBlock Mage_Adminhtml_Block_Tax_Rule_Edit_Form */
            $editRuleFormBlock = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Tax_Rule_Edit_Form');
            $editRuleFormBlock->toHtml();
            $taxRateElement = $editRuleFormBlock->getForm()->getElement('tax_rate');
            /** @var $taxRatePopUpBlock Mage_Adminhtml_Block_Tax_Rate_Form */
            $taxRatePopUpBlock = $this->getLayout()->createBlock('Mage_Adminhtml_Block_Tax_Rate_Form');

            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
                'success' => true,
                'error_message' => '',
                'tax_rate_field' => $taxRateElement->toHtml(),
                'tax_rate_collection' => $taxRatePopUpBlock->getRateCollection(),
            ));
        } catch (Exception $e) {
            $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
                'success' => false,
                'error_message' => Mage::helper('Mage_Launcher_Helper_Data')->__($e->getMessage()),
                'tax_rate_field' => '',
                'tax_rate_collection' => '',
            ));
        }
        $this->getResponse()->setBody($responseContent);
    }

    /**
     * Import Tax Rates
     * @todo It's a copy-paste from Mage_Adminhtml_Tax_RateController. This logic initially had to be put in the model.
     * @todo Refactor Mage_Adminhtml_Tax_RateController::importPostAction().
     *
     * @throws Mage_Core_Exception
     */
    public function _importRates()
    {
        $ratesFile = $this->getRequest()->getFiles('import_rates_file');
        if (empty($ratesFile)) {
            Mage::throwException('Invalid file upload attempt.');
        }
        $csvContainer  = new Varien_File_Csv();
        $csvData = $csvContainer->getData($ratesFile['tmp_name']);

        /** checks columns */
        $csvFields  = array(
            0   => Mage::helper('Mage_Tax_Helper_Data')->__('Code'),
            1   => Mage::helper('Mage_Tax_Helper_Data')->__('Country'),
            2   => Mage::helper('Mage_Tax_Helper_Data')->__('State'),
            3   => Mage::helper('Mage_Tax_Helper_Data')->__('Zip/Post Code'),
            4   => Mage::helper('Mage_Tax_Helper_Data')->__('Rate'),
            5   => Mage::helper('Mage_Tax_Helper_Data')->__('Zip/Post is Range'),
            6   => Mage::helper('Mage_Tax_Helper_Data')->__('Range From'),
            7   => Mage::helper('Mage_Tax_Helper_Data')->__('Range To')
        );


        $stores = array();
        $unset = array();
        $storeCollection = Mage::getModel('Mage_Core_Model_Store')->getCollection()->setLoadDefault(false);
        $cvsFieldsNum = count($csvFields);
        $cvsDataNum   = count($csvData[0]);
        for ($i = $cvsFieldsNum; $i < $cvsDataNum; $i++) {
            $header = $csvData[0][$i];
            $found = false;
            foreach ($storeCollection as $store) {
                if ($header == $store->getCode()) {
                    $csvFields[$i] = $store->getCode();
                    $stores[$i] = $store->getId();
                    $found = true;
                }
            }
            if (!$found) {
                $unset[] = $i;
            }

        }

        $regions = array();

        if ($unset) {
            foreach ($unset as $u) {
                unset($csvData[0][$u]);
            }
        }
        if ($csvData[0] == $csvFields) {

            foreach ($csvData as $k => $v) {
                if ($k == 0) {
                    continue;
                }

                //end of file has more then one empty lines
                if (count($v) <= 1 && !strlen($v[0])) {
                    continue;
                }
                if ($unset) {
                    foreach ($unset as $u) {
                        unset($v[$u]);
                    }
                }

                if (count($csvFields) != count($v)) {
                    Mage::throwException('Invalid file upload attempt.');
                }

                $country = Mage::getModel('Mage_Directory_Model_Country')->loadByCode($v[1], 'iso2_code');
                if (!$country->getId()) {
                    Mage::throwException('One of the country has invalid code.');
                    continue;
                }

                if (!isset($regions[$v[1]])) {
                    $regions[$v[1]]['*'] = '*';
                    $regionCollection = Mage::getModel('Mage_Directory_Model_Region')->getCollection()
                        ->addCountryFilter($v[1]);
                    if ($regionCollection->getSize()) {
                        foreach ($regionCollection as $region) {
                            $regions[$v[1]][$region->getCode()] = $region->getRegionId();
                        }
                    }
                }

                if (!empty($regions[$v[1]][$v[2]])) {
                    $rateData  = array(
                        'code'           => $v[0],
                        'tax_country_id' => $v[1],
                        'tax_region_id'  => ($regions[$v[1]][$v[2]] == '*') ? 0 : $regions[$v[1]][$v[2]],
                        'tax_postcode'   => (empty($v[3]) || $v[3] == '*') ? null : $v[3],
                        'rate'           => $v[4],
                        'zip_is_range'   => $v[5],
                        'zip_from'       => $v[6],
                        'zip_to'         => $v[7]
                    );

                    $rateModel = Mage::getModel('Mage_Tax_Model_Calculation_Rate')->loadByCode($rateData['code']);
                    foreach($rateData as $dataName => $dataValue) {
                        $rateModel->setData($dataName, $dataValue);
                    }

                    $titles = array();
                    foreach ($stores as $field=>$id) {
                        $titles[$id] = $v[$field];
                    }

                    $rateModel->setTitle($titles);
                    $rateModel->save();
                }
            }
        } else {
            Mage::throwException('Invalid file format upload attempt');
        }
    }
}
