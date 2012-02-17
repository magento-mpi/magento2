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
 * @category    Mage
 * @package     Mage_PHPUnit
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Singleton class with static data needed for various objects using in TestCase.
 *
 * @category    Mage
 * @package     Mage_PHPUnit
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_PHPUnit_StaticDataPoolContainer
{
    /**
     * Instance of container
     *
     * @var Mage_PHPUnit_StaticDataPoolContainer
     */
    static protected $_instance;

    /**
     * Keys for pools with static data
     *
     * @var string
     */
    const POOL_REAL_MODEL_CLASSES           = 'classes';
    const POOL_REAL_BLOCK_CLASSES           = 'block_classes';
    const POOL_MODEL_DELEGATORS             = 'model';
    const POOL_RESOURCE_MODEL_DELEGATORS    = 'resource_model';
    const POOL_RESOURCE_MODEL_NAMES         = 'resource_model_name';
    const POOL_BLOCK_DELEGATORS             = 'block';

    /**
     * Class map for pool keys
     *
     * @var array
     */
    protected $_poolClasses = array(
        self::POOL_REAL_MODEL_CLASSES          => 'Mage_PHPUnit_StaticDataPool_ModelClass',
        self::POOL_REAL_BLOCK_CLASSES          => 'Mage_PHPUnit_StaticDataPool_ModelClass',
        self::POOL_MODEL_DELEGATORS            => 'Mage_PHPUnit_StaticDataPool_Model',
        self::POOL_RESOURCE_MODEL_DELEGATORS   => 'Mage_PHPUnit_StaticDataPool_Model',
        self::POOL_BLOCK_DELEGATORS            => 'Mage_PHPUnit_StaticDataPool_Model',
        self::POOL_RESOURCE_MODEL_NAMES        => 'Mage_PHPUnit_StaticDataPool_ResourceModelName'
    );

    /**
     * Pools container
     *
     * @var array array of poolKey => poolObject
     */
    protected $_pools = array();

    /**
     * Protected constructor.
     * Should be a singleton.
     */
    protected function __construct()
    {
    }

    /**
     * For singleton the clone method should be hidden
     */
    protected function __clone()
    {
    }

    /**
     * Creates and returns instance of the object
     *
     * @return Mage_PHPUnit_StaticDataPoolContainer
     */
    static public function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * Static method to return pool object by poolKey
     *
     * @param string $poolKey
     * @return Mage_PHPUnit_StaticDataPool_Abstract
     */
    static function getStaticDataObject($poolKey)
    {
        return self::getInstance()->getDataObject($poolKey);
    }

    /**
     * Returns pool object by poolKey. Public edition.
     *
     * @param string $poolKey
     * @return Mage_PHPUnit_StaticDataPool_Abstract
     */
    public function getDataObject($poolKey)
    {
        if (!isset($this->_pools[$poolKey])) {
            $poolClass =  $this->_poolClasses[$poolKey];
            $this->_pools[$poolKey] = new $poolClass();
        }

        return $this->_pools[$poolKey];
    }

    /**
     * Sets pool object to the pool container
     *
     * @param string $poolKey
     * @param Mage_PHPUnit_StaticDataPool_Abstract|object $dataObject
     */
    public function setDataObject($poolKey, $dataObject)
    {
        return $this->_pools[$poolKey] = $dataObject;
    }

    /**
     * Remove pool object from the container
     *
     * @param string $poolKey
     */
    public function removeDataObject($poolKey)
    {
        if (isset($this->_pools[$poolKey])) {
            $this->_callBeforeClean($this->_pools[$poolKey]);
            unset($this->_pools[$poolKey]);
        }
    }

    /**
     * Calls pool's beforeClean() method if it can be called
     *
     * @param Mage_PHPUnit_StaticDataPool_Abstract|object $pool
     */
    protected function _callBeforeClean($pool)
    {
        if (is_object($pool) && $pool instanceof Mage_PHPUnit_StaticDataPool_Abstract) {
            $pool->beforeClean();
        }
    }

    /**
     * Cleans all pool objects
     */
    public function clean()
    {
        foreach ($this->_pools as $pool) {
            $this->_callBeforeClean($pool);
        }
        $this->_pools = array();
    }
}
