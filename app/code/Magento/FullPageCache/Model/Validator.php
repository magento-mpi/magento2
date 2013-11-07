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
    /**
     * @var array
     */
    protected $_changeDependencies = array();

    /**
     * @var array
     */
    protected $_deleteDependencies = array();

    /**
     * @var \Magento\Core\Model\Cache\TypeListInterface
     */
    protected $_typeList;

    /**
     * @param \Magento\Core\Model\Cache\TypeListInterface $typeList
     * @param $changeDependencies
     * @param $deleteDependencies
     */
    public function __construct(
        \Magento\Core\Model\Cache\TypeListInterface $typeList,
        $changeDependencies,
        $deleteDependencies
    ) {
        $this->_typeList = $typeList;
        $this->_changeDependencies = $changeDependencies;
        $this->_deleteDependencies = $deleteDependencies;
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
        $intersect = array_intersect($this->_changeDependencies, $classes);
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
        $intersect = array_intersect($this->_deleteDependencies, $classes);
        if (!empty($intersect)) {
            $this->_invalidateCache();
        }
        return $this;
    }
}
