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
     * @var string[]
     */
    protected $_changeDependencies = array();

    /**
     * @var string[]
     */
    protected $_deleteDependencies = array();

    /**
     * @var \Magento\App\Cache\TypeListInterface
     */
    protected $_typeList;

    /**
     * @param \Magento\App\Cache\TypeListInterface $typeList
     * @param string[] $changeDependencies
     * @param string[] $deleteDependencies
     */
    public function __construct(
        \Magento\App\Cache\TypeListInterface $typeList,
        $changeDependencies,
        $deleteDependencies
    ) {
        $this->_typeList = $typeList;
        $this->_changeDependencies = $changeDependencies;
        $this->_deleteDependencies = $deleteDependencies;
    }

    /**
     * Mark full page cache as invalidated
     *
     * @return void
     */
    protected function _invalidateCache()
    {
        $this->_typeList->invalidate('full_page');
    }

    /**
     * Get list of all classes related with object instance
     *
     * @param object $object
     * @return string[]
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
     * Check if during data change was used some model related with page cache and invalidate cache
     *
     * @param object $object
     * @return $this
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
     * Check if during data delete was used some model related with page cache and invalidate cache
     *
     * @param object $object
     * @return $this
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
