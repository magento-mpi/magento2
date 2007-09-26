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
 * @category   Mage
 * @package    Mage_Core
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Core_Model_Layout_Update
{
    /**
     * Layout Update Simplexml Element Class Name
     *
     * @var string
     */
    protected $_elementClass;

    /**
     * @var Simplexml_Element
     */
    protected $_packageLayout;

    /**
     * Cache object
     *
     * @var Zend_Cache_Core
     */
    protected $_cache;

    /**
     * Cache key
     *
     * @var string
     */
    protected $_cacheId;

    /**
     * Cache prefix
     *
     * @var string
     */
    protected $_cachePrefix;

    /**
     * Cumulative array of update XML strings
     *
     * @var array
     */
    protected $_updates = array();

    /**
     * Handles used in this update
     *
     * @var array
     */
    protected $_handles = array();

    /**
     * Substitution values in structure array('from'=>array(), 'to'=>array())
     *
     * @var array
     */
    protected $_subst = array();

    public function __construct()
    {
        $subst = Mage::getConfig()->getPathVars();
        foreach ($subst as $k=>$v) {
            $this->_subst['from'][] = '{{'.$k.'}}';
            $this->_subst['to'][] = $v;
        }
    }

    public function getElementClass()
    {
        if (!$this->_elementClass) {
            $this->_elementClass = Mage::getConfig()->getModelClassName('core/layout_element');
        }
        return $this->_elementClass;
    }

    public function resetUpdates()
    {
        $this->_updates = array();
        return $this;
    }

    public function addUpdate($update)
    {
        $this->_updates[] = $update;
        return $this;
    }

    public function asArray()
    {
        return $this->_updates;
    }

    public function asString()
    {
        return implode('', $this->_updates);
    }

    public function resetHandles()
    {
        $this->_handles = array();
        return $this;
    }

    public function addHandle($handle)
    {
        $this->_handles[$handle] = 1;
        return $this;
    }

    public function removeHandle($handle)
    {
        unset($this->_handles[$handle]);
        return $this;
    }

    public function getHandles()
    {
        return array_keys($this->_handles);
    }

    /**
     * Get cache id
     *
     * @return string
     */
    public function getCacheId()
    {
        if (!$this->_cacheId) {
            $this->_cacheId = md5(join('__', $this->getHandles()));
        }
        return $this->_cacheId;
    }

    /**
     * Set cache id
     *
     * @param string $cacheId
     * @return Mage_Core_Model_Layout_Update
     */
    public function setCacheId($cacheId)
    {
        $this->_cacheId = $cacheId;
        return $this;
    }

    /**
     * Get Layout Updates Cache Object
     *
     * @return Zend_Cache_Core
     */
    public function getCache()
    {
        if (!$this->_cache) {
            $this->_cache = Zend_Cache::factory('Core', 'File', array(), array(
                'cache_dir'=>Mage::getBaseDir('cache_layout')
            ));
        }
        return $this->_cache;
    }

    public function loadCache()
    {
        $result = $this->getCache()->load($this->getCacheId());
        if (false===$result) {
            return false;
        }
        if (!Mage::useCache('layout')) {
            $this->getCache()->clean();
            return false;
        }

        $this->addUpdate($result);

        return true;
    }

    public function saveCache()
    {
        if (!Mage::useCache('layout')) {
            return false;
        }
        $str = $this->asString();
        return $this->getCache()->save($str, $this->getCacheId(), $this->getHandles());
    }

    /**
     * Load layout updates by handles
     *
     * @param array|string $handles
     * @return Mage_Core_Model_Layout_Update
     */
    public function load($handles=array())
    {
        if (is_string($handles)) {
            $handles = array($handles);
        } elseif (!is_array($handles)) {
            throw Mage::exception('Mage_Core', __('Invalid layout update handle'));
        }

        foreach ($handles as $handle) {
            $this->addHandle($handle);
        }

        if ($this->loadCache()) {
            return $this;
        }

        foreach ($this->getHandles() as $handle) {
            $this->merge($handle);
        }

        $this->saveCache();
        return $this;
    }

    public function asSimplexml()
    {
        $updates = trim($this->asString());
        if (empty($updates)) {
            $updates = '<layout/>';
        }
        $updates = '<'.'?xml version="1.0"?'.'><layout>'.$updates.'</layout>';
        return simplexml_load_string($updates, $this->getElementClass());
    }

    /**
     * Merge layout update by handle
     *
     * @param string $handle
     * @return Mage_Core_Model_Layout_Update
     */
    public function merge($handle)
    {
        if (!$this->fetchPackageLayoutUpdates($handle)
            && !$this->fetchDbLayoutUpdates($handle)) {
            #$this->removeHandle($handle);
        }

        return $this;
    }

    public function fetchPackageLayoutUpdates($handle)
    {
        $_profilerKey = 'layout/package_update: '.$handle;
        Varien_Profiler::start($_profilerKey);

        if (empty($this->_packageLayout)) {
            $mainFilename = Mage::getSingleton('core/design_package')->getLayoutFilename('main.xml');
            if (!is_readable($mainFilename)) {
                throw Mage::exception('Mage_Core', __('Package layout file (main.xml) could not be read.'));
            }
            $layoutStr = file_get_contents($mainFilename);
            $layoutStr = str_replace($this->_subst['from'], $this->_subst['to'], $layoutStr);
            $layoutXml = simplexml_load_string($layoutStr, $this->getElementClass());
            if (!$layoutXml) {
                throw Mage::exception('Mage_Core', __('Could not load default layout file'));
            }
            $this->_packageLayout = $layoutXml;
        }

        if (!($updateXml = $this->_packageLayout->$handle)) {
            Varien_Profiler::stop($_profilerKey);
            return false;
        }

        $this->fetchRecursiveUpdates($updateXml);

        $this->addUpdate($updateXml->innerXml());

        Varien_Profiler::stop($_profilerKey);
        return true;
    }

    public function fetchDbLayoutUpdates($handle)
    {
        $_profilerKey = 'layout/db_update: '.$handle;
        Varien_Profiler::start($_profilerKey);

        try {
            $updateStr = Mage::getResourceModel('core/layout')->fetchUpdatesByHandle($handle);
            if (!$updateStr) {
                return false;
            }
            $updateStr = str_replace($this->_subst['from'], $this->_subst['to'], $updateStr);
            $updateXml = simplexml_load_string($updateStr, $this->getElementClass());
            $this->fetchRecursiveUpdates($updateXml);

            $this->addUpdate($update);
        } catch (PDOException $e) {
            throw $e;
        } catch (Exception $e) {

        }

        Varien_Profiler::stop($_profilerKey);
        return true;
    }

    public function fetchRecursiveUpdates($updateXml)
    {
        foreach ($updateXml->children() as $child) {
            if (strtolower($child->getName())=='update' && isset($child['handle'])) {
                $this->merge((string)$child['handle']);
            }
        }
        return $this;
    }
}