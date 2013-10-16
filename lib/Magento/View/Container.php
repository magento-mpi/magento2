<?php

namespace Magento\View;

use Magento\View\Render\Html;

interface Container
{
    public function & getMeta();

    public function getName();

    public function getType();

    public function getChildren();


    public function addHandle($handleName);


    /**
     * @return Container
     */
    public function getParentElement();


    /**
     * @param $name
     * @return Container
     */
    public function getElement($name);

    public function removeElement($name);

    public function renderElement($name, $type = Html::TYPE_HTML);


    /**
     * @param $alias
     * @return Container
     */
    public function getChild($alias);

    public function getChildrenElements();

    public function isBlock(Container $element);

    public function isContainer(Container $element);

    public function removeChildrenElements();

    public function renderChildren($type = Html::TYPE_HTML);



    public function register(Container $parent = null);

    public function attach(Container $child, $alias = null, $before = null, $after = null);

    public function detach(Container $child, $alias = null);

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
