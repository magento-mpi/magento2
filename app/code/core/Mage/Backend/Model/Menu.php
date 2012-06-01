<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Backend_Model_Menu extends ArrayIterator
{
    /**
     * @var int
     */
    protected $_sortIndex = 0;

    protected $_path = '';

    public function __construct($array = array())
    {
        if (isset($array['path'])) {
            $this->_path = $array['path'] . '/';
            unset($array['path']);
        }
        parent::__construct($array);
    }

    public function next()
    {
        parent::next();
        if ($this->valid() && ($this->current()->isDisabled() || !$this->current()->isAllowed())) {
            $this->next();
        }
    }

    public function rewind()
    {
        $this->ksort();
        parent::rewind();
        if ($this->valid() && (current($this)->isDisabled() || !(current($this)->isAllowed()))) {
            $this->next();
        }
    }

    public function addChild(Mage_Backend_Model_Menu_Item $item, $index = null)
    {
        $index = !is_null($index) ? $index : ($item->hasSortIndex() ? $item->getSortIndex() : $this->_sortIndex++);
        if (!isset($this[$index])) {
            $this->offsetSet($index, $item);
            $item->setParent($this);
        } else {
            $this->addChild($item, $index + 1);
        }
    }

    public function isLast(Mage_Backend_Model_Menu_Item $item)
    {
        return false;//end($this->getArrayCopy()) == $item;
    }

    public function setPath($path)
    {
        $this->_path = $path . '/';
        foreach ($this as $child) {
            $child->setParent($this);
        }
    }

    public function getFullPath()
    {
        return $this->_path;
    }

    /**
     * Find first menu item that user is able to access
     *
     * @param Mage_Core_Model_Config_Element $parent
     * @param string $path
     * @param integer $level
     * @return string|null
     */
    public function getFirstAvailableChild()
    {
        foreach ($this as $item) {
            /** @var $item Mage_Backend_Model_Menu_Item */
            if ($item->isAllowed()) {
                return $item->getFirstAvailableChild();
            }
        }
        return null;
    }
}
