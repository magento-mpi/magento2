<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\ObjectManager\Profiler\Tree;

class Item
{
    protected $class;

    protected $parent = null;

    protected $hash = null;

    protected $children = array();

    public function __construct($class, Item $parent = null)
    {
        $this->class = $class;
        $this->parent = $parent;

        if ($parent) {
            $parent->addChild($this);
        }
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @param Item $item
     */
    public function addChild(Item $item)
    {
        $this->children[] = $item;
    }

    /**
     * @return array[Item]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return Item|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
}
