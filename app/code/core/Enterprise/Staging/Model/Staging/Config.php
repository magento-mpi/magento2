<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Staging config model
 */
class Enterprise_Staging_Model_Staging_Config
{
    /**
     * Staging type codes
     */
    const TYPE_WEBSITE          = 'website';

    const DEFAULT_TYPE          = 'website';

    /**
     * Staging states
     */
    const STATE_NEW             = 'new';
    const STATE_PROCESSING      = 'processing';
    const STATE_COMPLETE        = 'complete';
    const STATE_CLOSED          = 'closed';
    const STATE_CANCELED        = 'canceled';
    const STATE_MERGED          = 'merged';
    const STATE_REVERTED        = 'reverted';
    const STATE_BROKEN          = 'broken';
    const STATE_RESTORED        = 'restored';
    const STATE_HOLDED          = 'holded';

    /**
     * Staging statuses
     */
    const STATUS_NEW            = 'new';
    const STATUS_PROCESSING     = 'processing';
    const STATUS_COMPLETE       = 'complete';
    const STATUS_CLOSED         = 'closed';
    const STATUS_CANCELED       = 'canceled';
    const STATUS_MERGED         = 'merged';
    const STATUS_REVERTED       = 'reverted';
    const STATUS_BROKEN         = 'broken';
    const STATUS_RESTORED       = 'restored';
    const STATUS_HOLDED         = 'holded';
    const STATUS_FAIL           = 'failed';

    /**
     * Staging event codes
     */
    const EVENT_CREATE          = 'create';
    const EVENT_SAVE            = 'save';
    const EVENT_BACKUP          = 'backup';
    const EVENT_MERGE           = 'merge';
    const EVENT_ROLLBACK        = 'rollback';

    const STORAGE_METHOD_PREFIX = 'table_prefix';
    const STORAGE_METHOD_NEW_DB = 'new_db';

    /**
     * Staging visibility codes
     */
    const VISIBILITY_NOT_ACCESSIBLE             = 'not_accessible';
    const VISIBILITY_ACCESSIBLE                 = 'accessible';
    const VISIBILITY_REQUIRE_HTTP_AUTH          = 'require_http_auth';

    static $_stagingItems;

    /**
     * Retrieve staging module xml config as Varien_Simplexml_Element object
     *
     * @param   string $path
     * @return  object Varien_Simplexml_Element
     */
    static public function getConfig($path = null)
    {
        $_path = 'global/enterprise/staging/';
        if (!is_null($path)) {
            $_path .= ltrim($path, '/');
        }
        return Mage::getConfig()->getNode($_path);
    }

    /**
     * Staging instance abstract factory
     *
     * @param   string $model
     * @param   Enterprise_Staging_Model_Staging $staging
     * @param   bool $singleton
     * @return  Enterprise_Staging_Model_Staging_Type_Abstract
     */
    public static function factory($model, $staging, $singleton = false)
    {
        $types = self::getConfig('type');
        $stagingType = $staging->getType();
        if (is_null($stagingType)) {
            $stagingType = self::DEFAULT_TYPE;
        }
        $typeConfig = $types->{$stagingType}->asArray();

        if (!empty($typeConfig['models'][$model])) {
            $modelName = $typeConfig['models'][$model];
        } else {
            throw new Enterprise_Staging_Exception(Mage::helper('enterprise_staging')->__('Need to specify class name for %s model',$model) );
        }

        if ($singleton === true) {
            $model = Mage::getSingleton($modelName);
        } else {
            $model = Mage::getModel($modelName);
        }

        $model->setStaging($staging);
        $model->setConfig($typeConfig);

        return $model;
    }

    /**
     * Staging type instance factory
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @param   bool $singleton
     * @return  Enterprise_Staging_Model_Staging_Type_Abstract
     */
    public static function typeFactory($staging, $singleton = false)
    {
        return self::factory('type', $staging, $singleton);
    }

    /**
     * Staging type mapper instance factory
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @param   boolean $singleton
     * @return  Enterprise_Staging_Model_Staging_Mapper_Abstract
     */
    public static function mapperFactory($staging, $singleton = false)
    {
        return self::factory('mapper', $staging, $singleton);
    }

    /**
     * Staging resource adapter mapper instance factory
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @param   boolean $singleton
     * @return  Enterprise_Staging_Model_Staging_Adapter_Abstract
     */
    public static function adapterFactory($staging, $singleton = false)
    {
        return self::factory('adapter', $staging, $singleton);
    }

    /**
     * Staging state instance factory
     *
     * @param   Enterprise_Staging_Model_Staging $staging
     * @param   boolean $singleton
     * @return  Enterprise_Staging_Model_Staging_State_Abstract
     */
    public static function stateFactory($staging, $singleton = false)
    {
        return self::factory('state', $staging, $singleton);
    }

    /**
     * get Config node as mixed option array
     *
     * @param string $nodeName
     * @return mixed
     */
    static public function getOptionArray($nodeName='type')
    {
        $options = array();
        $config = self::getConfig($nodeName);
        foreach($config->children() as $node) {
            $options[$node->getName()] = (string) $node->label;
        }

        return $options;
    }

    /**
     * get Config node as mixed option array with empty first element
     *
     * @param string $nodeName
     * @return mixed
     */
    static public function getAllOption($nodeName='type')
    {
        $options = self::getOptionArray($nodeName);
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

    /**
     * get Config node as mixed option array, with selected structure: value, label
     * with empty first element
     *
     * @param string $nodeName
     * @return mixed
     */
    static public function getAllOptions($nodeName='type')
    {
        $res = array();
        $res[] = array('value'=>'', 'label'=>'');
        foreach (self::getOptionArray($nodeName) as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * get Config node as mixed option array, with selected structure: value, label
     *
     * @param string $nodeName
     * @return mixed
     */
    static public function getOptions($nodeName='type')
    {
        $res = array();
        foreach (self::getOptionArray($nodeName) as $index => $value) {
            $res[] = array(
               'value' => $index,
               'label' => $value
            );
        }
        return $res;
    }

    /**
     * get Config node as text by option id
     *
     * @param int $optionId
     * @param string $nodeName
     * @return text
     */
    static public function getOptionText($optionId, $nodeName)
    {
        $options = self::getOptionArray($nodeName);
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    /**
     * create staging item as simpleXml
     *
     * @return simpleXml
     */
    static public function getStagingItems()
    {
        if (is_null(self::$_stagingItems)) {
            $stagingItems = self::getConfig('staging_items');

            foreach($stagingItems->children() AS $item_id => $item) {
                if (!self::isItemModuleActive($item)) {
                     continue;
                }
            }

            self::$_stagingItems = $stagingItems;
        }

        return self::$_stagingItems;
    }

    static function isItemModuleActive($stagingItem)
    {
        $module = (string) $stagingItem->module;
        if (!empty($module)) {
            $moduleConfig = Mage::getConfig()->getModuleConfig($module);
            if ($moduleConfig) {
                if ('false' === (string)$moduleConfig->active) {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * get staging item
     *
     * @param string $itemCode
     * @return string
     */
    static public function getStagingItem($itemCode)
    {
        $stagingItems = self::getStagingItems();

        if (!empty($stagingItems->{$itemCode})) {
            return $stagingItems->{$itemCode};
        } else {
            foreach ($stagingItems->children() as $stagingItem) {
                if ($stagingItem->extends) {
                    if ($stagingItem->extends->{$itemCode}) {
                        return $stagingItem->extends->{$itemCode};
                    }
                }
            }
            return null;
        }
    }

    /**
     * get used storage method
     *
     * @return string
     */
    static public function getUsedStorageMethod()
    {
        return (string) self::getConfig('use_storage_method');
    }

    /**
     * Retrieve default status for state
     *
     * @param   string $state
     * @return  string
     */
    static public function getStateDefaultStatus($state)
    {
        $status = false;
        if ($stateNode = self::getConfig('state/'.$state)) {
            if ($stateNode->statuses) {
                foreach ($stateNode->statuses->children() as $statusNode) {
                    if (!$status) {
                        $status = $statusNode->getName();
                    }
                    $attributes = $statusNode->attributes();
                    if (isset($attributes['default'])) {
                        $status = $statusNode->getName();
                    }
                }
            }
        }
        return $status;
    }

    /**
     * Retrieve state label
     *
     * @param   string $state
     * @return  string
     */
    static public function getStateLabel($state)
    {
        if ($stateNode = self::getConfig('state/'.$state)) {
            $state = (string) $stateNode->label;
            return Mage::helper('enterprise_staging')->__($state);
        }
        return $state;
    }

    /**
     * Retrieve status label
     *
     * @param   string $status
     * @return  string
     */
    static public function getStatusLabel($status)
    {
        if ($statusNode = self::getConfig('status/'.$status)) {
            $status = (string) $statusNode->label;
            return Mage::helper('enterprise_staging')->__($status);
        }
        return $status;
    }

    /**
     * Retrieve visibility label
     *
     * @param   string $visibility
     * @return  string
     */
    static public function getVisibilityLabel($visibility)
    {
        if ($visibilityNode = self::getConfig('visibility/'.$visibility)) {
            $visibility = (string) $visibilityNode->label;
            return Mage::helper('enterprise_staging')->__($visibility);
        }
        return $visibility;
    }

    /**
     * Retrieve staging table prefix
     *
     * @param   object $object
     * @param   string $internalPrefix
     * @return  string
     */
    static public function getTablePrefix($object = null, $internalPrefix = '')
    {
        $globalTablePrefix  = (string) Mage::getConfig()->getTablePrefix();

        $stagingTablePrefix = self::getStagingTablePrefix();

        if (!is_null($object)) {
            $stagingTablePrefix = $object->getTablePrefix();
        } else {
            $stagingTablePrefix = $globalTablePrefix . $stagingTablePrefix;
        }

        $stagingTablePrefix  .=  $internalPrefix;

        return $stagingTablePrefix;
    }

    /**
     * Get staging global table prefix
     *
     * @return string
     */
    static public function getStagingTablePrefix()
    {
        return (string) self::getConfig('global_staging_table_prefix');
    }

    /**
     * Get staging global table prefix
     * @param string $internalPrefix
     * @return string
     */
    static public function getBackupTablePrefix($internalPrefix)
    {
        $backupPrefix = Enterprise_Staging_Model_Staging_Config::getStagingBackupTablePrefix();

        if (is_object($internalPrefix)) {
            $backupPrefix .= $internalPrefix . "_";
        }
        return $backupPrefix;
    }
    /**
     * Get staging global table prefix
     *
     * @return string
     */
    static public function getStagingBackupTablePrefix()
    {
        return (string) self::getConfig('global_staging_backup_table_prefix');
    }

    /**
     * Get table name by item config info
     *
     * @param string $tableName
     * @param string $modelEntity
     * @param Mage_Core_Model_Website $stagingWebsite
     *
     * @return string
     */
    static public function getStagingTableName($tableName, $modelEntity, $stagingWebsite = null)
    {
        $staging = Mage::getModel('enterprise_staging/staging');
        if (!is_null($stagingWebsite)) {
            $staging->loadByStagingWebsiteId($stagingWebsite->getId());
        }
        if (!Mage::registry("staging/frontend_checked")) {
            $staging->checkFrontend($staging);
        }

        list($model, $entity) = split("[/]" , $modelEntity, 2);
        if (!$model){
            return $tableName;
        }

        $globalTablePrefix = (string) Mage::getConfig()->getTablePrefix();

        $stagingTablePrefix = self::getTablePrefix();
        if (empty($stagingTablePrefix)){
            return $tableName;
        }

        $tableName = $globalTablePrefix . $tableName;

        if (self::isStagingUpTableName($model, $tableName)) {
            $tableName = $stagingTablePrefix . $tableName;
        }

        return $tableName;

    }

    /**
     * Check in staging config ig need to modify src table name
     *
     * @param string $model
     * @param string $tableName
     * @return bool
     */
    static public function isStagingUpTableName($model, $tableName)
    {
        $itemSet = self::getConfig("staging_items");

        if (is_object($itemSet)) {
            foreach($itemSet->children() as $item) {
                $itemModel = (string) $item->model;
                if ($itemModel == $model) {
                    $isBackend = (string) $item->is_backend;
                    $useStorageMethod = (string) $item->use_storage_method;
                    if ($isBackend && $useStorageMethod == "table_prefix") {
                        //apply prefix for custom tables
                        if (!empty($item->entities) && is_object($item->entities)){
                            foreach($item->entities->children() AS $entity) {
                                $entityTable = (string) $entity->table;
                                if (!empty($entityTable) && $entityTable == $tableName) {
                                    return true;
                                }
                            }
                        } else {
                             return true;
                        }
                    }
                }
            }
        }
        return false;
    }


    /**
     * Retrieve core resources version
     *
     * @return  string
     */
    static public function getCoreResourcesVersion()
    {
        $coreResource = Mage::getSingleton('core/resource');
        $connection = $coreResource->getConnection('core_read');
        $select = $connection->select()->from($coreResource->getTableName('core/resource'), array('code' , 'version'));
        $result = $connection->fetchPairs($select);
        if (is_array($result) && count($result)>0) {
            return $result;
        } else {
            return array();
        }
    }


    /**
     * Retrieve event label
     *
     * @param   string $event
     * @return  string
     */
    static public function getEventLabel($event)
    {
        if ($eventNode = self::getConfig('event/'.$event)) {
            $event = (string) $eventNode->label;
            return Mage::helper('enterprise_staging')->__($event);
        }
        return $event;
    }
}
