<?php

namespace Magento\View;

use Magento\View\Render\Html;

interface Element
{
    public function & getMeta();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getType();

    /**
     * @return array
     */
    public function getChildren();

    /**
     * @param string $handleName
     */
    public function addHandle($handleName);

    /**
     * Return parent element
     *
     * @return Element
     */
    public function getParentElement();

    /**
     * Return child element
     *
     * @param string $name
     * @return Element|null
     */
    public function getElement($name);

    /**
     * Remove child element
     *
     * @param string $name
     */
    public function removeElement($name);

    /**
     * @param string $name
     * @param string $type
     * @return string
     */
    public function renderElement($name, $type = Html::TYPE_HTML);

    /**
     * @param $alias
     * @return Element
     */
    public function getChild($alias);

    /**
     * Return array of children
     *
     * @return array
     */
    public function getChildrenElements();

    /**
     * Whether element is a block
     *
     * @param Element $element
     * @return bool
     */
    public function isBlock(Element $element);

    /**
     * Whether element is a container
     *
     * @param Element $element
     * @return bool
     */
    public function isContainer(Element $element);

    /**
     * Remove all children elements
     */
    public function removeChildrenElements();

    /**
     * @param string $type
     * @return string
     */
    public function renderChildren($type = Html::TYPE_HTML);

    /**
     * @param Element $parent
     */
    public function register(Element $parent = null);

    /**
     * @param Element $child
     * @param string|null $alias
     * @param string|null $before
     * @param string|null $after
     */
    public function attach(Element $child, $alias = null, $before = null, $after = null);

    /**
     * @param Element $child
     * @param null|string $alias
     */
    public function detach(Element $child, $alias = null);

    /**
     * @param string $type
     * @return string
     */
    public function render($type = Html::TYPE_HTML);

    /**
     * @return array
     */
    public function & getDataProviders();

    /**
     * @param string $name
     * @param \Magento\View\DataProvider $dataProvider
     */
    public function addDataProvider($name, $dataProvider);

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ALIASES for backward compatibility

    /**
     * alias for getChildrenElements
     *
     * @return array
     */
    public function getChildBlocks();

    /**
     * alias for renderElement($name, $type = Html::TYPE_HTML);
     *
     * @param string $name
     * @param string $type
     * @return string
     */
    public function getChildHtml($name, $type = Html::TYPE_HTML);
}
