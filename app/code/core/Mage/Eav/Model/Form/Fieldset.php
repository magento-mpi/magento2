<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Eav Form Fieldset Model
 *
 * @method Mage_Eav_Model_Resource_Form_Fieldset _getResource()
 * @method Mage_Eav_Model_Resource_Form_Fieldset getResource()
 * @method int getTypeId()
 * @method Mage_Eav_Model_Form_Fieldset setTypeId(int $value)
 * @method string getCode()
 * @method Mage_Eav_Model_Form_Fieldset setCode(string $value)
 * @method int getSortOrder()
 * @method Mage_Eav_Model_Form_Fieldset setSortOrder(int $value)
 *
 * @category    Mage
 * @package     Mage_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Eav_Model_Form_Fieldset extends Mage_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'eav_form_fieldset';

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Eav_Model_Resource_Form_Fieldset');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Mage_Eav_Model_Resource_Form_Fieldset
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve resource collection instance wrapper
     *
     * @return Mage_Eav_Model_Resource_Form_Fieldset_Collection
     */
    public function getCollection()
    {
        return parent::getCollection();
    }

    /**
     * Validate data before save data
     *
     * @throws Mage_Core_Exception
     * @return Mage_Eav_Model_Form_Fieldset
     */
    protected function _beforeSave()
    {
        if (!$this->getTypeId()) {
            Mage::throwException(Mage::helper('Mage_Eav_Helper_Data')->__('Invalid form type.'));
        }
        if (!$this->getStoreId() && $this->getLabel()) {
            $this->setStoreLabel($this->getStoreId(), $this->getLabel());
        }

        return parent::_beforeSave();
    }

    /**
     * Retrieve fieldset labels for stores
     *
     * @return array
     */
    public function getLabels()
    {
        if (!$this->hasData('labels')) {
            $this->setData('labels', $this->_getResource()->getLabels($this));
        }
        return $this->_getData('labels');
    }

    /**
     * Set fieldset store labels
     * Input array where key - store_id and value = label
     *
     * @param array $labels
     * @return Mage_Eav_Model_Form_Fieldset
     */
    public function setLabels(array $labels)
    {
        return $this->setData('labels', $labels);
    }

    /**
     * Set fieldset store label
     *
     * @param int $storeId
     * @param string $label
     * @return Mage_Eav_Model_Form_Fieldset
     */
    public function setStoreLabel($storeId, $label)
    {
        $labels = $this->getLabels();
        $labels[$storeId] = $label;

        return $this->setLabels($labels);
    }

    /**
     * Retrieve label store scope
     *
     * @return int
     */
    public function getStoreId()
    {
        if (!$this->hasStoreId()) {
            $this->setData('store_id', Mage::app()->getStore()->getId());
        }
        return $this->_getData('store_id');
    }
}
