<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View;

use Magento\View\Render\Html;

interface Container
{
    /**
     * Retrieve container configuration data
     *
     * @return array
     */
    public function &getMeta();

    /**
     * Retrieve container name
     *
     * @return string
     */
    public function getName();

    /**
     * Retrieve container type
     *
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
     * @return Container
     */
    public function getParentElement();

    /**
     * Retrieve container by name
     *
     * @param string $name
     * @return Container
     */
    public function getElement($name);

    /**
     * Remove container by name
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
     * @param string $alias
     * @return Container
     */
    public function getChild($alias);

    /**
     * Retrieve children
     *
     * @return array
     */
    public function getChildrenElements();

    /**
     * Whether element is a block
     *
     * @param Container $element
     * @return bool
     */
    public function isBlock(Container $element);

    /**
     * Whether element is a container
     *
     * @param Container $element
     * @return bool
     */
    public function isContainer(Container $element);

    /**
     * Remove children
     */
    public function removeChildrenElements();

    /**
     * @param string $type
     * @return string
     */
    public function renderChildren($type = Html::TYPE_HTML);

    /**
     * @param Container $parent
     */
    public function register(Container $parent = null);

    /**
     * Attach container
     *
     * @param Container $child
     * @param string|null $alias
     * @param string|null $before
     * @param string|null $after
     */
    public function attach(Container $child, $alias = null, $before = null, $after = null);

    /**
     * Detach container
     *
     * @param Container $child
     * @param null|string $alias
     */
    public function detach(Container $child, $alias = null);

    /**
     * @param string $type
     * @return string
     */
    public function render($type = Html::TYPE_HTML);

    /**
     * Retrieve assigned data providers
     *
     * @return array
     */
    public function &getDataProviders();

    /**
     * Add data provider
     *
     * @param string $name
     * @param \Magento\View\DataProvider $dataProvider
     */
    public function addDataProvider($name, $dataProvider);

    /**
     * Alias for getChildrenElements
     *
     * @return array
     */
    public function getChildBlocks();

    /**
     * Alias for renderElement
     *
     * @param string $name
     * @param string $type
     * @return string
     */
    public function getChildHtml($name, $type = Html::TYPE_HTML);
}
