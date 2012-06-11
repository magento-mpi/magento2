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
     * @param string $parentId
     * @param int $index
     * @throws InvalidArgumentException
     */
    public function add(Mage_Backend_Model_Menu_Item $item, $parentId = null, $index = null)
    {
        if ($this->get($item->getId())) {
            throw new InvalidArgumentException('Item with id ' . $item->getId() . ' already exists in tree');
        }
        echo $parentId;
        if ($parentId) {
            $this->get($parentId)->getChildren()->add($item, null, $index);
        } else {
            $index = !is_null($index) ? $index : $this->_sortIndex++;
            if (!isset($this[$index])) {
                $this->_maxIndex = $this->_maxIndex < $index ? $index : $this->_maxIndex;
                $this->offsetSet($index, $item);
                $item->setPath($this->getFullPath());
            } else {
                $this->add($item, $parentId, $index + 1);
            }
        }
    }

    /**
     * Retrieve menu item by id
     *
     * @param string $itemId
     * @return Mage_Backend_Model_Menu_Item|null
     */
    public function get($itemId)
    {
        $result = null;
        foreach ($this as $item) {
            /** @var $item Mage_Backend_Model_Menu_Item */
            if ($item->getId() == $itemId) {
                $result = $item;
                break;
            }

            if ($item->hasChildren() && ($result = $item->getChildren()->get($itemId))) {
                break;
            }
        }
        return $result;
    }

    /**
     * Move menu item
     *
     * @param string $itemId
     * @param string $toItemId
     * @param int $sortIndex
     */
    public function move($itemId, $toItemId, $sortIndex = null)
    {
        $item = $this->get($itemId);
        $this->remove($itemId);
        $this->add($item, $toItemId, $sortIndex);
    }

    /**
     * Remove menu item by id
     *
     * @param string $itemId
     * @return bool
     */
    public function remove($itemId)
    {
        $result = false;
        foreach ($this as $key => $item) {
            /** @var $item Mage_Backend_Model_Menu_Item */
            if ($item->getId() == $itemId) {
                unset($this[$key]);
                $result = true;
                break;
            }

            if ($item->hasChildren() && ($result = $item->getChildren()->remove($itemId))) {
                break;
            }
        }
        return $result;
    }

    /**
     * Change order of an item in its parent menu
     *
     * @param string $itemId
     * @param int $position
     * @return bool
     */
    public function reorder($itemId, $position)
    {
        $result = false;
        foreach ($this as $key => $item) {
            /** @var $item Mage_Backend_Model_Menu_Item */
            if ($item->getId() == $itemId) {
                unset($this[$key]);
                $this->add($item, null, $position);
                $result = true;
                break;
            } else if ($item->hasChildren() && $result = $item->getChildren()->reorder($itemId, $position)) {
                break;
            }
        }
        return $result;
    }

    /**
     * Check whether provided item is last in list
     *
     * @param Mage_Backend_Model_Menu_Item $item
     * @return bool
     */
    public function isLast(Mage_Backend_Model_Menu_Item $item)
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
            $child->setPath($this->getFullPath());
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
    public function getFirstAvailable()
    {
        $result = null;
        foreach ($this as $item) {
            /** @var $item Mage_Backend_Model_Menu_Item */
            if ($item->hasChildren() && $result = $item->getChildren()->getFirstAvailable()) {
                break;
            } else {
                $result = $item;
                break;
            }
        }
        return $result;
    }
}
