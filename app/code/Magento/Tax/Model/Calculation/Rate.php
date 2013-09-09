<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax Rate Model
 *
 * @method Magento_Tax_Model_Resource_Calculation_Rate _getResource()
 * @method Magento_Tax_Model_Resource_Calculation_Rate getResource()
 * @method string getTaxCountryId()
 * @method Magento_Tax_Model_Calculation_Rate setTaxCountryId(string $value)
 * @method int getTaxRegionId()
 * @method Magento_Tax_Model_Calculation_Rate setTaxRegionId(int $value)
 * @method string getTaxPostcode()
 * @method Magento_Tax_Model_Calculation_Rate setTaxPostcode(string $value)
 * @method string getCode()
 * @method Magento_Tax_Model_Calculation_Rate setCode(string $value)
 * @method float getRate()
 * @method Magento_Tax_Model_Calculation_Rate setRate(float $value)
 * @method int getZipIsRange()
 * @method Magento_Tax_Model_Calculation_Rate setZipIsRange(int $value)
 * @method int getZipFrom()
 * @method Magento_Tax_Model_Calculation_Rate setZipFrom(int $value)
 * @method int getZipTo()
 * @method Magento_Tax_Model_Calculation_Rate setZipTo(int $value)
 *
 * @category    Magento
 * @package     Magento_Tax
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Tax_Model_Calculation_Rate extends Magento_Core_Model_Abstract
{
    protected $_titles = null;
    protected $_titleModel = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_eventManager = $eventManager;
        parent::__construct($context, $resource, $resourceCollection, $data);
    }

    /**
     * Magento model constructor
     */
    protected function _construct()
    {
        $this->_init('Magento_Tax_Model_Resource_Calculation_Rate');
    }

    /**
     * Prepare location settings and tax postcode before save rate
     *
     * @return Magento_Tax_Model_Calculation_Rate
     */
    protected function _beforeSave()
    {
        $isWrongRange = $this->getZipIsRange() && ($this->getZipFrom() === '' || $this->getZipTo() === '');

        $isEmptyValues = $this->getCode() === '' || $this->getTaxCountryId() === '' || $this->getRate() === ''
            || $this->getTaxPostcode() === '';

        if ($isEmptyValues || $isWrongRange) {
            Mage::throwException(__('Please fill all required fields with valid information.'));
        }

        if (!is_numeric($this->getRate()) || $this->getRate() <= 0) {
            Mage::throwException(__('Rate Percent should be a positive number.'));
        }

        if ($this->getZipIsRange()) {
            $zipFrom = $this->getZipFrom();
            $zipTo = $this->getZipTo();

            if (strlen($zipFrom) > 9 || strlen($zipTo) > 9) {
                Mage::throwException(__('Maximum zip code length is 9.'));
            }

            if (!is_numeric($zipFrom) || !is_numeric($zipTo) || $zipFrom < 0 || $zipTo < 0) {
                Mage::throwException(__('Zip code should not contain characters other than digits.'));
            }

            if ($zipFrom > $zipTo) {
                Mage::throwException(__('Range To should be equal or greater than Range From.'));
            }

            $this->setTaxPostcode($zipFrom . '-' . $zipTo);
        } else {
            $taxPostCode = $this->getTaxPostcode();

            if (strlen($taxPostCode) > 10) {
                $taxPostCode = substr($taxPostCode, 0, 10);
            }

            $this->setTaxPostcode($taxPostCode)
                ->setZipIsRange(null)
                ->setZipFrom(null)
                ->setZipTo(null);
        }

        parent::_beforeSave();
        $country = $this->getTaxCountryId();
        $region = $this->getTaxRegionId();
        $regionModel = Mage::getModel('Magento_Directory_Model_Region');
        $regionModel->load($region);
        if ($regionModel->getCountryId() != $country) {
            $this->setTaxRegionId('*');
        }
        return $this;
    }

    /**
     * Save rate titles
     *
     * @return Magento_Tax_Model_Calculation_Rate
     */
    protected function _afterSave()
    {
        $this->saveTitles();
        $this->_eventManager->dispatch('tax_settings_change_after');
        return parent::_afterSave();
    }

    /**
     * Processing object before delete data
     *
     * @return Magento_Core_Model_Abstract
     * @throws Magento_Core_Exception
     */
    protected function _beforeDelete()
    {
        if ($this->_isInRule()) {
            Mage::throwException(__('The tax rate cannot be removed. It exists in a tax rule.'));
        }
        return parent::_beforeDelete();
    }

    /**
     * After rate delete
     * redeclared for dispatch tax_settings_change_after event
     *
     * @return Magento_Tax_Model_Calculation_Rate
     */
    protected function _afterDelete()
    {
        $this->_eventManager->dispatch('tax_settings_change_after');
        return parent::_afterDelete();
    }

    public function saveTitles($titles = null)
    {
        if (is_null($titles)) {
            $titles = $this->getTitle();
        }

        $this->getTitleModel()->deleteByRateId($this->getId());
        if (is_array($titles) && $titles) {
            foreach ($titles as $store=>$title) {
                if ($title !== '') {
                    $this->getTitleModel()
                        ->setId(null)
                        ->setTaxCalculationRateId($this->getId())
                        ->setStoreId((int) $store)
                        ->setValue($title)
                        ->save();
                }
            }
        }
    }

    public function getTitleModel()
    {
        if (is_null($this->_titleModel)) {
            $this->_titleModel = Mage::getModel('Magento_Tax_Model_Calculation_Rate_Title');
        }
        return $this->_titleModel;
    }

    public function getTitles()
    {
        if (is_null($this->_titles)) {
            $this->_titles = $this->getTitleModel()->getCollection()->loadByRateId($this->getId());
        }
        return $this->_titles;
    }

    public function deleteAllRates()
    {
        $this->_getResource()->deleteAllRates();
        $this->_eventManager->dispatch('tax_settings_change_after');
        return $this;
    }

    /**
     * Load rate model by code
     *
     * @param  string $code
     * @return Magento_Tax_Model_Calculation_Rate
     */
    public function loadByCode($code)
    {
        $this->load($code, 'code');
        return $this;
    }


    /**
     * Check if rate exists in tax rule
     *
     * @return array
     */
    protected function _isInRule()
    {
        return $this->getResource()->isInRule($this->getId());
    }
}
