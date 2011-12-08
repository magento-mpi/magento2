<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Google Optimizer Scripts Model
 *
 * @method Mage_GoogleOptimizer_Model_Resource_Code _getResource()
 * @method Mage_GoogleOptimizer_Model_Resource_Code getResource()
 * @method Mage_GoogleOptimizer_Model_Code setEntityId(int $value)
 * @method Mage_GoogleOptimizer_Model_Code setEntityType(string $value)
 * @method int getStoreId()
 * @method Mage_GoogleOptimizer_Model_Code setStoreId(int $value)
 * @method string getControlScript()
 * @method Mage_GoogleOptimizer_Model_Code setControlScript(string $value)
 * @method string getTrackingScript()
 * @method Mage_GoogleOptimizer_Model_Code setTrackingScript(string $value)
 * @method string getConversionScript()
 * @method Mage_GoogleOptimizer_Model_Code setConversionScript(string $value)
 * @method string getConversionPage()
 * @method Mage_GoogleOptimizer_Model_Code setConversionPage(string $value)
 * @method string getAdditionalData()
 * @method Mage_GoogleOptimizer_Model_Code setAdditionalData(string $value)
 *
 * @category    Mage
 * @package     Mage_GoogleOptimizer
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_GoogleOptimizer_Model_Code extends Mage_Core_Model_Abstract
{
    protected $_entity = null;
    protected $_entityType = null;
    protected $_validateEntryFlag = false;
    protected $_scriptTypes = array('control', 'tracking', 'conversion');

    protected function _construct()
    {
        parent::_construct();
        $this->_init('Mage_GoogleOptimizer_Model_Resource_Code');
    }

    /**
     * Set entity
     *
     * @param Varien_Object $entity
     * @return Mage_GoogleOptimizer_Model_Code
     */
    public function setEntity(Varien_Object $entity)
    {
        $this->_entity = $entity;
        return $this;
    }

    /**
     * Return entity
     *
     * @return unknown
     */
    public function getEntity()
    {
        return $this->_entity;
    }

    /**
     * Return entity type (product|category|...etc)
     *
     * @return string
     */
    public function getEntityType()
    {
        return $this->_entityType;
    }

    /**
     * Loading scripts and assigning scripts on entity
     *
     * @param Varien_Object $entity
     * @return Mage_GoogleOptimizer_Model_Code
     */
    public function loadScripts($storeId)
    {
        if (is_null($this->getEntity()) || is_null($this->getEntityType())) {
            return $this;
        }

        if (!$storeId) {
            $storeId = Mage::app()->getStore()->getId();
        }

        $this->getResource()->loadByEntityType($this, $storeId);
        $this->_afterLoad();
        return $this;
    }

    /**
     * Validate sctipts that assigned on entity
     *
     * @return bool
     */
    protected function _validate()
    {
        $entryFlag = false;
        $validationResult = false;
        if ($control = $this->getControlScript()) {
            $entryFlag = true;
        }
        if ($tracking = $this->getTrackingScript()) {
            $entryFlag = true;
        }
        if ($conversion = $this->getConversionScript()) {
            $entryFlag = true;
        }
        if ($conversionPage = $this->getConversionPage()) {
            $entryFlag = true;
        }
        $this->_validateEntryFlag = $entryFlag;
        if ($entryFlag && (!$control || !$tracking || !$conversion || !$conversionPage)) {
            return false;
        }
        return true;
    }

    /**
     * Save scripts assigned on entity
     *
     * @param Varien_Object $entity
     * @return Mage_GoogleOptimizer_Model_Code
     */
    public function saveScripts($storeId)
    {
        if (is_null($this->getEntity()) || is_null($this->getEntityType())) {
            return $this;
        }
        if (!$this->getEntity()->getGoogleOptimizerScripts()) {
            return $this;
        }
        $script = $this->getEntity()->getGoogleOptimizerScripts();

        $this->setData($script->getData())
            ->setEntityId($this->getEntity()->getId())
            ->setEntityType($this->getEntityType());

        /**
         * We can't modify store id if existing stcript
         */
        if (!$script->getId()) {
            $this->setStoreId($storeId);
        }

        if (false === $this->_validate()) {
            throw new Exception(Mage::helper('Mage_GoogleOptimizer_Helper_Data')->__('All fields of script types have to be filled.'));
        }

        // use default scripts, need to delete scripts for current store
        if ($this->getStoreFlag()) {
            $this->deleteScripts($storeId);
            return $this;
        }

        $this->save();
        return $this;
    }

    /**
     * Removing scripts assigned to entity
     *
     * @param integer $storeId
     * @return Mage_GoogleOptimizer_Model_Code
     */
    public function deleteScripts($storeId)
    {
        if (is_null($this->getEntity()) || is_null($this->getEntityType())) {
            return $this;
        }
        $this->getResource()->deleteByEntityType($this, $storeId);
        return $this;
    }
}
