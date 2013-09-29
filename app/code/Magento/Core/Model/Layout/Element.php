<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */


namespace Magento\Core\Model\Layout;

class Element extends \Magento\Simplexml\Element
{
    public function prepare($args)
    {
        switch ($this->getName()) {
            case \Magento\Core\Model\Layout::TYPE_BLOCK:
                $this->prepareBlock($args);
                break;

            case \Magento\Core\Model\Layout::TYPE_REFERENCE_BLOCK:
            case \Magento\Core\Model\Layout::TYPE_REFERENCE_CONTAINER:
                $this->prepareReference($args);
                break;

            case \Magento\Core\Model\Layout::TYPE_ACTION:
                $this->prepareAction($args);
                break;

            case \Magento\Core\Model\Layout::TYPE_ARGUMENT:
                $this->prepareActionArgument($args);
                break;

            default:
                break;
        }
        foreach ($this as $child) {
            $child->prepare($args);
        }
        return $this;
    }

    public function getBlockName()
    {
        $tagName = (string)$this->getName();
        if (empty($this['name']) || !in_array($tagName, array(
                \Magento\Core\Model\Layout::TYPE_BLOCK,
                \Magento\Core\Model\Layout::TYPE_REFERENCE_BLOCK,
        ))) {
            return false;
        }
        return (string)$this['name'];
    }

    /**
     * Get element name
     *
     * Advanced version of getBlockName() method: gets name for container as well as for block
     *
     * @return string|bool
     */
    public function getElementName()
    {
        $tagName = $this->getName();
        if (!in_array($tagName, array(
            \Magento\Core\Model\Layout::TYPE_BLOCK,
            \Magento\Core\Model\Layout::TYPE_REFERENCE_BLOCK,
            \Magento\Core\Model\Layout::TYPE_CONTAINER,
            \Magento\Core\Model\Layout::TYPE_REFERENCE_CONTAINER
        ))) {
            return false;
        }
        return $this->getAttribute('name');
    }

    /**
     * Extracts sibling from 'before' and 'after' attributes
     *
     * @return string
     */
    public function getSibling()
    {
        $sibling = null;
        if ($this->getAttribute('before')) {
            $sibling = $this->getAttribute('before');
        } elseif ($this->getAttribute('after')) {
            $sibling = $this->getAttribute('after');
        }

        return $sibling;
    }

    public function prepareBlock($args)
    {
        $parent = $this->getParent();
        if (isset($parent['name']) && !isset($this['parent'])) {
            $this->addAttribute('parent', (string)$parent['name']);
        }

        return $this;
    }

    public function prepareReference($args)
    {
        return $this;
    }

    public function prepareAction($args)
    {
        $parent = $this->getParent();
        $this->addAttribute('block', (string)$parent['name']);

        return $this;
    }

    public function prepareActionArgument($args)
    {
        return $this;
    }
}
