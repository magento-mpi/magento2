<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Tab Interface
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Ui\Component\Layout\Tabs;

interface TabInterface
{
    /**
     * Return Tab label
     *
     * @return string
     */
    public function getTabLabel();

    /**
     * Return Tab title
     *
     * @return string
     */
    public function getTabTitle();

    /**
     * Tab class getter
     *
     * @return string
     */
    public function getTabClass();

    /**
     * Return URL link to Tab content
     *
     * @return string
     */
    public function getTabUrl();

    /**
     * Tab should be loaded trough Ajax call
     *
     * @return bool
     */
    public function isAjaxLoaded();

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    public function canShowTab();

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    public function isHidden();

    /**
     * Retrieve Tab content
     *
     * @return string
     */
    public function toHtml();
}
