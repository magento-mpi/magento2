<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging config model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Model_Staging_Config
{
    /**
     * Available staging types
     */
    const TYPE_WEBSITE      = 'website';
    const TYPE_CATALOG      = 'catalog';
    const TYPE_CUSTOMER     = 'customer';
    const TYPE_SALES        = 'sales';
    const TYPE_CMS          = 'cms';
    const TYPE_CONFIG       = 'config';
    const TYPE_CONFIGURABLE = 'configurable';

    const DEFAULT_TYPE              = 'website';

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

    const VISIBILITY_NOT_ACCESSIBLE             = 'not_accessible';
    const VISIBILITY_ACCESSIBLE                 = 'accessible';
    const VISIBILITY_REQUIRE_HTTP_AUTH          = 'require_http_auth';
    const VISIBILITY_REQUIRE_ADMIN_SESSION      = 'require_admin_session';
    const VISIBILITY_REQUIRE_BOTH               = 'require_both';


    static private $_config;

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

        // TODO need to try to give current staging into models as an attribute
        $model->setStaging($staging->getId());

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
     * @param   bool $singleton
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
     * @param   bool $singleton
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
     * @param   bool $singleton
     * @return  Enterprise_Staging_Model_Staging_State_Abstract
     */
    public static function stateFactory($staging, $singleton = false)
    {
        return self::factory('state', $staging, $singleton);
    }

    static public function getOptionArray($nodeName='type')
    {
        $options = array();
        $config = self::getConfig($nodeName);
        foreach($config->children() as $node) {
            $options[$node->getName()] = (string) $node->label;
        }

        return $options;
    }

    static public function getAllOption($nodeName='type')
    {
        $options = self::getOptionArray($nodeName);
        array_unshift($options, array('value'=>'', 'label'=>''));
        return $options;
    }

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

    static public function getOptionText($optionId, $nodeName)
    {
        $options = self::getOptionArray($nodeName);
        return isset($options[$optionId]) ? $options[$optionId] : null;
    }

    static public function getStagingItems()
    {
        return self::getConfig('staging_items');
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
     * Retrieve status label
     *
     * @param   string $status
     * @return  string
     */
    public function getStatusLabel($status)
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
    public function getVisibilityLabel($visibility)
    {
        if ($visibilityNode = self::getConfig('visibility/'.$visibility)) {
            $visibility = (string) $visibilityNode->label;
            return Mage::helper('enterprise_staging')->__($visibility);
        }
        return $visibility;
    }

    /**
     * Retrieve event label
     *
     * @param   string $event
     * @return  string
     */
    public function getEventLabel($event)
    {
        if ($eventNode = self::getConfig('event/'.$event)) {
            $event = (string) $eventNode->label;
            return Mage::helper('enterprise_staging')->__($event);
        }
        return $event;
    }
}