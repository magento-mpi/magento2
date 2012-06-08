<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend menu model
 */
class Mage_Backend_Model_Menu extends ArrayObject
{
    /**
     * Sort index used to add items without sort index explicitly set
     *
     * @var int
     */
    protected $_sortIndex = 0;

    /**
     * Max index in array
     *
     * @var int
     */
    protected $_maxIndex = 0;

    /**
     * Path in tree structure
     *
     * @var string
     */
    protected $_path = '';

    /**
     * @param array $array
     */
    public function __construct($array = array())
    {
        if (isset($array['path'])) {
            $this->_path = $array['path'] . '/';
            unset($array['path']);
        }
        $this->setIteratorClass('Mage_Backend_Model_Menu_Iterator');
        parent::__construct($array);
    }

    /**
     * Add child to menu item
     *
     * @param Mage_Backend_Model_Menu_Item $item
     * @param int $index
     */
    public function addChild(Mage_Backend_Model_Menu_Item $item, $index = null)
    {
        $index = !is_null($index) ? $index : ($item->hasSortIndex() ? $item->getSortIndex() : $this->_sortIndex++);
        if (!isset($this[$index])) {
            $this->_maxIndex = $this->_maxIndex < $index ? $index : $this->_maxIndex;
            $this->offsetSet($index, $item);
            $item->setParent($this);
        } else {
            $this->addChild($item, $index + 1);
        }
    }

    /**
     * Retrieve menu item by id
     *
     * @param string $itemId
     * @param bool $searchRecursive search item recursive
     * @return Mage_Backend_Model_Menu_Item|null
     */
    public function getChildById($itemId, $searchRecursive = false)
    {
        $result = null;
        foreach ($this as $item) {
            if ($item->getId() == $itemId) {
                $result = $item;
                break;
            }

            if ($searchRecursive &&
                $item->hasChildren() &&
                ($result = $item->getChildren()->getChildById($itemId, $searchRecursive))
            ) {
                break;
            }
        }
        return $result;
    }

    /**
     * Remove menu item by id
     *
     * @param string $itemId
     * @return bool
     */
    public function removeChildById($itemId)
    {
        $result = false;
        foreach ($this as $key => $item) {
            if ($item->getId() == $itemId) {
                unset($this[$key]);
                $result = true;
                break;
            }

            if ($item->hasChildren() && ($result = $item->getChildren()->removeChildById($itemId))) {
                break;
            }
        }
        return $result;
    }

    /**
     * @param string $itemId
     * @param int $position
     */
    public function reorderChildById($itemId, $position)
    {
        foreach ($this as $key => $item) {
            if ($item->getId() == $itemId) {
                unset($this[$key]);
                $this->addChild($item, $position);
                break;
            }
        }
    }

    /**
     * Check whether provided item is last in list
     *
     * @param Mage_Backend_Model_Menu_Item $item
     * @return bool
     */
    public function isChildLast(Mage_Backend_Model_Menu_Item $item)
    {
        return $this->offsetGet($this->_maxIndex)->getId() == $item->getId();
    }

    /**
     * Set path in tree
     *
     * @param $path
     */
    public function setPath($path)
    {
        $this->_path = $path . '/';
        foreach ($this as $child) {
            $child->setParent($this);
        }
    }

    /**
     * Retrieve full path to node in tree
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->_path;
    }

    /**
     * Find first menu item that user is able to access
     *
     * @return Mage_Backend_Model_Menu_Item|null
     */
    public function getFirstAvailableChild()
    {
        foreach ($this as $item) {
            /** @var $item Mage_Backend_Model_Menu_Item */
            return $item->getFirstAvailableChild();
        }
        return null;
    }
}
