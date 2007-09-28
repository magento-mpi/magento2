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
 
/**
 * Abstract helper
 *
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
abstract class Mage_Core_Helper_Abstract
{
    /**
     * Cache object
     *
     * @var Zend_Cache_Frontend_File
     */
    protected static $_cache;
    
    /**
     * Helper module name
     *
     * @var string
     */
    protected $_moduleName;
    
    /**
     * Rrtrieve helper cache object
     *
     * @return Zend_Cache_Frontend_File
     */
    protected function _getCache()
    {
        if (!self::$_cache) {
            self::$_cache = Zend_Cache::factory('Core', 'File',
                array(),
                array('cache_dir'=>Mage::getBaseDir('cache_helper'))
            );
        }
        return self::$_cache;
    }
    
    /**
     * Loading cache data
     *
     * @param   string $id
     * @return  mixed
     */
    protected function _loadCache($id)
    {
        return $this->_getCache()->load($id);
    }
    
    /**
     * Saving cache
     *
     * @param   mixed $data
     * @param   string $id
     * @param   array $tags
     * @return  Mage_Core_Helper_Abstract
     */
    protected function _saveCache($data, $id, $tags=array())
    {
        $this->_getCache()->save($data, $id, $tags);
        return $this;
    }
    
    /**
     * Removing cache
     *
     * @param   string $id
     * @return  Mage_Core_Helper_Abstract
     */
    protected function _removeCache($id)
    {
        $this->_getCache()->remove($id);
        return $this;
    }
    
    /**
     * Cleaning cache
     *
     * @param   array $tags
     * @return  Mage_Core_Helper_Abstract
     */
    protected function _cleanCache($tags=array())
    {
        if (empty($tags)) {
            $this->_getCache()->clean(Zend_Cache::CLEANING_MODE_ALL);
        }
        else {
            $this->_getCache()->clean(Zend_Cache::CLEANING_MODE_MATCHING_TAG, $tags);
        }
        return $this;
    }
    
    /**
     * Retrieve helper module name
     *
     * @return string
     */
    protected function _getModuleName()
    {
        if (!$this->_moduleName) {
            $class = get_class($this);
            $this->_moduleName = substr($class, 0, strpos($class, '_Helper'));
        }
        return $this->_moduleName;
    }
    
    /**
     * Translate
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->_getModuleName());
        array_unshift($args, $expr);
        return Mage::app()->getTranslator()->translate($args);
    }
}
