<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_FullPageCache
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\FullPageCache\Model;

class Validator
{
    /**#@+
     * XML paths for lists of change nad delete dependencies
     */
    const XML_PATH_DEPENDENCIES_CHANGE = 'adminhtml/cache/dependency/change';
    const XML_PATH_DEPENDENCIES_DELETE = 'adminhtml/cache/dependency/delete';
    /**#@-*/

    /**
     * General config object
     *
     * @var \Magento\Core\Model\Config
     */
    protected $_config;

    /**
     * @var \Magento\Core\Model\Cache\TypeListInterface
     */
    protected $_typeList;

    /**
     * Constructor dependency injection
     *
     * @param \Magento\Core\Model\Cache\TypeListInterface $typeList
     * @param \Magento\Core\Model\Config $coreConfig
     */
    public function __construct(
        \Magento\Core\Model\Cache\TypeListInterface $typeList,
        \Magento\Core\Model\Config $coreConfig
    ) {
        $this->_typeList = $typeList;
        $this->_config = $coreConfig;
    }

    /**
     * Mark full page cache as invalidated
     */
    protected function _invalidateCache()
    {
        $this->_typeList->invalidate('full_page');
    }

    /**
     * Get list of all classes related with object instance
     *
     * @param $object
     * @return array
     */
    protected function _getObjectClasses($object)
    {
        $classes = array();
        if (is_object($object)) {
            $classes[] = get_class($object);
            $parent = $object;
            while ($parentClass = get_parent_class($parent)) {
                $classes[] = $parentClass;
                $parent = $parentClass;
            }
        }
        return $classes;
    }

    /**
     * Check if duering data change was used some model related with page cache and invalidate cache
     *
     * @param mixed $object
     * @return \Magento\FullPageCache\Model\Validator
     */
    public function checkDataChange($object)
    {
        $classes = $this->_getObjectClasses($object);
        $intersect = array_intersect($this->_getDataChangeDependencies(), $classes);
        if (!empty($intersect)) {
            $this->_invalidateCache();
        }

        return $this;
    }

    /**
     * Check if duering data delete was used some model related with page cache and invalidate cache
     *
     * @param mixed $object
     * @return \Magento\FullPageCache\Model\Validator
     */
    public function checkDataDelete($object)
    {
        $classes = $this->_getObjectClasses($object);
        $intersect = array_intersect($this->_getDataDeleteDependencies(), $classes);
        if (!empty($intersect)) {
            $this->_invalidateCache();
        }
        return $this;
    }

    /**
     * Returns array of data change dependencies from config
     *
     * @return array
     */
    protected function _getDataChangeDependencies()
    {
        $dependencies = $this->_config->getNode(self::XML_PATH_DEPENDENCIES_CHANGE)
            ->asArray();
        return array_values($dependencies);
    }

    /**
     * Returns array of data delete dependencies from config
     *
     * @return array
     */
    protected function _getDataDeleteDependencies()
    {
        $dependencies = $this->_config->getNode(self::XML_PATH_DEPENDENCIES_DELETE)
            ->asArray();
        return array_values($dependencies);
    }
}
