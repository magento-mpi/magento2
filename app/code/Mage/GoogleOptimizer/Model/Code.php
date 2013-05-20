<?php
/**
 * Google Experiment Code Model
 *
 * @method Mage_GoogleOptimizer_Model_Resource_Code _getResource()
 * @method Mage_GoogleOptimizer_Model_Resource_Code getResource()
 * @method Mage_GoogleOptimizer_Model_Code setEntityId(int $value)
 * @method string getEntityId()
 * @method Mage_GoogleOptimizer_Model_Code setEntityType(string $value)
 * @method string getEntityType()
 * @method Mage_GoogleOptimizer_Model_Code setStoreId(int $value)
 * @method int getStoreId()
 * @method Mage_GoogleOptimizer_Model_Code setExperimentScript(int $value)
 * @method string getExperimentScript()
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Mage_GoogleOptimizer_Model_Code extends Mage_Core_Model_Abstract
{
    /**
     * Entity type product
     */
    const CODE_ENTITY_TYPE_PRODUCT = 'product';

    /**
     * Entity type category
     */
    const CODE_ENTITY_TYPE_CATEGORY = 'category';

    /**
     * Entity type category
     */
    const CODE_ENTITY_TYPE_CMS = 'cms';

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
        $this->_init('Mage_GoogleOptimizer_Model_Resource_Code');
    }

    /**
     * Loading scripts and assigning scripts on entity
     *
     * @param $entityId
     * @param $entityType
     * @param $storeId
     * @return $this
     */
    public function loadScripts($entityId, $entityType, $storeId = 0)
    {
        $this->getResource()->loadByEntityType($this, $entityId, $entityType, $storeId);
        $this->_afterLoad();
        return $this;
    }
}
