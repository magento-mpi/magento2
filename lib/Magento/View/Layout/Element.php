<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Layout;

class Element extends \Magento\Simplexml\Element
{
    /**#@+
     * Supported layout directives
     */
    const TYPE_BLOCK = 'block';
    const TYPE_CONTAINER = 'container';
    const TYPE_ACTION = 'action';
    const TYPE_ARGUMENTS = 'arguments';
    const TYPE_ARGUMENT = 'argument';
    const TYPE_REFERENCE_BLOCK = 'referenceBlock';
    const TYPE_REFERENCE_CONTAINER = 'referenceContainer';
    const TYPE_REMOVE = 'remove';
    const TYPE_MOVE = 'move';
    /**#@-*/

    public function prepare()
    {
        switch ($this->getName()) {
            case self::TYPE_BLOCK:
                $this->prepareBlock();
                break;

            case self::TYPE_REFERENCE_BLOCK:
            case self::TYPE_REFERENCE_CONTAINER:
                $this->prepareReference();
                break;

            case self::TYPE_ACTION:
                $this->prepareAction();
                break;

            case self::TYPE_ARGUMENT:
                $this->prepareActionArgument();
                break;

            default:
                break;
        }
        foreach ($this as $child) {
            $child->prepare();
        }
        return $this;
    }

    public function getBlockName()
    {
        $tagName = (string)$this->getName();
        if (empty($this['name']) || !in_array($tagName, array(
                self::TYPE_BLOCK,
                self::TYPE_REFERENCE_BLOCK,
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
            self::TYPE_BLOCK,
            self::TYPE_REFERENCE_BLOCK,
            self::TYPE_CONTAINER,
            self::TYPE_REFERENCE_CONTAINER
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

    /**
     * Add parent element name to parent attribute
     *
     * @return Element
     */
    public function prepareBlock()
    {
        $parent = $this->getParent();
        if (isset($parent['name']) && !isset($this['parent'])) {
            $this->addAttribute('parent', (string)$parent['name']);
        }

        return $this;
    }

    /**
     * @return Element
     */
    public function prepareReference()
    {
        return $this;
    }

    /**
     * Add parent element name to block attribute
     *
     * @return Element
     */
    public function prepareAction()
    {
        $parent = $this->getParent();
        $this->addAttribute('block', (string)$parent['name']);

        return $this;
    }

    /**
     * @return Element
     */
    public function prepareActionArgument()
    {
        return $this;
    }
}
