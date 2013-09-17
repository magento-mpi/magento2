<?php
/**
 * Google Experiment Code Model
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 * @method Magento_GoogleOptimizer_Model_Resource_Code _getResource()
 * @method Magento_GoogleOptimizer_Model_Resource_Code getResource()
 * @method Magento_GoogleOptimizer_Model_Code setEntityId(int $value)
 * @method string getEntityId()
 * @method Magento_GoogleOptimizer_Model_Code setEntityType(string $value)
 * @method string getEntityType()
 * @method Magento_GoogleOptimizer_Model_Code setStoreId(int $value)
 * @method int getStoreId()
 * @method Magento_GoogleOptimizer_Model_Code setExperimentScript(int $value)
 * @method string getExperimentScript()
 */
class Magento_GoogleOptimizer_Model_Code extends Magento_Core_Model_Abstract
{
    /**#@+
     * Entity types
     */
    const ENTITY_TYPE_PRODUCT = 'product';
    const ENTITY_TYPE_CATEGORY = 'category';
    const ENTITY_TYPE_PAGE = 'cms';
    /**#@-*/

    /**
     * @var bool
     */
    protected $_validateEntryFlag = false;

    /**
     * Model construct that should be used for object initialization
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('Magento_GoogleOptimizer_Model_Resource_Code');
    }

    /**
     * Loading by entity id and type type
     *
     * @param int $entityId
     * @param string $entityType One of self::CODE_ENTITY_TYPE_
     * @param int $storeId
     * @return Magento_GoogleOptimizer_Model_Code
     */
    public function loadByEntityIdAndType($entityId, $entityType, $storeId = 0)
    {
        $this->getResource()->loadByEntityType($this, $entityId, $entityType, $storeId);
        $this->_afterLoad();
        return $this;
    }
}
