<?php
/**
 * {license_notice}
 *
 * @category   Varien
 * @package    Varien_Cache
 * @copyright  {copyright}
 * @license    {license_link}
 */

class Varien_Cache_Core extends Zend_Cache_Core
{
    /**
     * Available options
     *
     * ====> (array) backend_decorators :
     * - array of decorators to decorate cache backend. Each element of this array should contain:
     * -- 'class' - concrete decorator, descendant of Magento_Cache_Backend_Decorator_Abstract
     * -- 'options' - optional array of specific decorator options
     * @var array
     */
    protected $_specificOptions = array(
        'backend_decorators'    => array(),
    );

    /**
     * Make and return a cache id
     *
     * Checks 'cache_id_prefix' and returns new id with prefix or simply the id if null
     *
     * @param  string $cacheId Cache id
     * @return string Cache id (with or without prefix)
     */
    protected function _id($cacheId)
    {
        if ($cacheId !== null) {
            $cacheId = preg_replace('/([^a-zA-Z0-9_]{1,1})/', '_', $cacheId);
            if (isset($this->_options['cache_id_prefix'])) {
                $cacheId = $this->_options['cache_id_prefix'] . $cacheId;
            }
        }
        return $cacheId;
    }

    /**
     * Prepare tags
     *
     * @param array $tags
     * @return array
     */
    protected function _tags($tags)
    {
        foreach ($tags as $key=>$tag) {
            $tags[$key] = $this->_id($tag);
        }
        return $tags;
    }

    /**
     * Save some data in a cache
     *
     * @param  mixed $data                  Data to put in cache (can be another type than string if
     *                                      automatic_serialization is on)
     * @param  null|string $cacheId         Cache id (if not set, the last cache id will be used)
     * @param  array $tags                  Cache tags
     * @param  bool|int $specificLifetime   If != false, set a specific lifetime for this cache record
     *                                      (null => infinite lifetime)
     * @param  int $priority                integer between 0 (very low priority) and 10 (maximum priority) used by
     *                                      some particular backends
     * @return boolean                      True if no problem
     */
    public function save($data, $cacheId = null, $tags = array(), $specificLifetime = false, $priority = 8)
    {
        $tags = $this->_tags($tags);
        return parent::save($data, $cacheId, $tags, $specificLifetime, $priority);
    }

    /**
     * Clean cache entries
     *
     * Available modes are :
     * 'all' (default)  => remove all cache entries ($tags is not used)
     * 'old'            => remove too old cache entries ($tags is not used)
     * 'matchingTag'    => remove cache entries matching all given tags
     *                     ($tags can be an array of strings or a single string)
     * 'notMatchingTag' => remove cache entries not matching one of the given tags
     *                     ($tags can be an array of strings or a single string)
     * 'matchingAnyTag' => remove cache entries matching any given tags
     *                     ($tags can be an array of strings or a single string)
     *
     * @param  string       $mode
     * @param  array|string $tags
     * @throws Zend_Cache_Exception
     * @return boolean True if ok
     */
    public function clean($mode = 'all', $tags = array())
    {
        $tags = $this->_tags($tags);
        return parent::clean($mode, $tags);
    }

    /**
     * Return an array of stored cache ids which match given tags
     *
     * In case of multiple tags, a logical AND is made between tags
     *
     * @param array $tags array of tags
     * @return array array of matching cache ids (string)
     */
    public function getIdsMatchingTags($tags = array())
    {
        $tags = $this->_tags($tags);
        return parent::getIdsMatchingTags($tags);
    }

    /**
     * Return an array of stored cache ids which don't match given tags
     *
     * In case of multiple tags, a logical OR is made between tags
     *
     * @param array $tags array of tags
     * @return array array of not matching cache ids (string)
     */
    public function getIdsNotMatchingTags($tags = array())
    {
        $tags = $this->_tags($tags);
        return parent::getIdsNotMatchingTags($tags);
    }

    /**
     * Set the backend
     *
     * @param  Zend_Cache_Backend $backendObject
     * @return void
     */
    public function setBackend(Zend_Cache_Backend $backendObject)
    {
        $backendObject = $this->_decorateBackend($backendObject);
        return parent::setBackend($backendObject);
    }

    /**
     * Decorate cache backend with additional functionality
     *
     * @param Zend_Cache_Backend
     * @return Zend_Cache_Backend
     */
    protected function _decorateBackend(Zend_Cache_Backend $backendObject)
    {
        if (!is_array($this->_specificOptions['backend_decorators'])) {
            Zend_Cache::throwException("'backend_decorator' option should be an array");
        }

        foreach ($this->_specificOptions['backend_decorators'] as $decoratorName => $decoratorOptions) {
            if (!is_array($decoratorOptions) || !array_key_exists('class', $decoratorOptions)) {
                Zend_Cache::throwException("Concrete decorator options in '" . $decoratorName
                    . "' should be an array containing 'class' key" );
            }
            $classOptions = array_key_exists('options', $decoratorOptions) ? $decoratorOptions['options'] : array();
            $classOptions['concrete_backend'] = $backendObject;

            if (!class_exists($decoratorOptions['class'])) {
                Zend_Cache::throwException("Class specified in '" . $decoratorName . "' does not exist");
            }

            $backendObject = new $decoratorOptions['class']($classOptions);
            if (!($backendObject instanceof Magento_Cache_Backend_Decorator_DecoratorAbstract)) {
                Zend_Cache::throwException("Decorator in '" . $decoratorName
                    . "' should extend Magento_Cache_Backend_Decorator_DecoratorAbstract");
            }
        }

        return $backendObject;
    }
}
