<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Eav Form Fieldset Model
 *
 * @method Magento_Eav_Model_Resource_Form_Fieldset getResource()
 * @method int getTypeId()
 * @method Magento_Eav_Model_Form_Fieldset setTypeId(int $value)
 * @method string getCode()
 * @method Magento_Eav_Model_Form_Fieldset setCode(string $value)
 * @method int getSortOrder()
 * @method Magento_Eav_Model_Form_Fieldset setSortOrder(int $value)
 *
 * @category    Magento
 * @package     Magento_Eav
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Eav_Model_Form_Fieldset extends Magento_Core_Model_Abstract
{
    /**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'eav_form_fieldset';

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_StoreManager $storeManager,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Eav_Model_Resource_Form_Fieldset');
    }

    /**
     * Retrieve resource instance wrapper
     *
     * @return Magento_Eav_Model_Resource_Form_Fieldset
     */
    protected function _getResource()
    {
        return parent::_getResource();
    }

    /**
     * Retrieve resource collection instance wrapper
     *
     * @return Magento_Eav_Model_Resource_Form_Fieldset_Collection
     */
    public function getCollection()
    {
        return parent::getCollection();
    }

    /**
     * Validate data before save data
     *
     * @throws Magento_Core_Exception
     * @return Magento_Eav_Model_Form_Fieldset
     */
    protected function _beforeSave()
    {
        if (!$this->getTypeId()) {
            throw new Magento_Core_Exception(__('Invalid form type.'));
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
     * @return Magento_Eav_Model_Form_Fieldset
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
     * @return Magento_Eav_Model_Form_Fieldset
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
            $this->setData('store_id', $this->_storeManager->getStore()->getId());
        }
        return $this->_getData('store_id');
    }
}
