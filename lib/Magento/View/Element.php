<?php

namespace Magento\View;

use Magento\View\Render\Html;

interface Element
{
    public function & getMeta();

    public function getName();

    public function getType();

    public function getChildren();


    public function addHandle($handleName);


    /**
     * @return Element
     */
    public function getParentElement();


    /**
     * @param $name
     * @return Element
     */
    public function getElement($name);

    public function removeElement($name);

    public function renderElement($name, $type = Html::TYPE_HTML);


    /**
     * @param $alias
     * @return Element
     */
    public function getChild($alias);

    public function getChildrenElements();

    public function isBlock(Element $element);

    public function isContainer(Element $element);

    public function removeChildrenElements();

    public function renderChildren($type = Html::TYPE_HTML);



    public function register(Element $parent = null);

    public function attach(Element $child, $alias = null, $before = null, $after = null);

    public function detach(Element $child, $alias = null);

    public function render($type = Html::TYPE_HTML);



    public function & getDataProviders();

    public function addDataProvider($name, $dataProvider);


    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    // ALIASES for backward compatibility

    // alias for getChildrenElements
    public function getChildBlocks();

    // alias for renderElement($name, $type = Html::TYPE_HTML);
    public function getChildHtml($name, $type = Html::TYPE_HTML);
}
