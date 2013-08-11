<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product information tabs
 */
class Magento_Catalog_Block_Product_View_Tabs extends Magento_Core_Block_Template
{
    /**
     * Configured tabs
     *
     * @var array
     */
    protected $_tabs = array();

    /**
     * Add tab to the container
     *
     * @param string $alias
     * @param string $title
     * @param string $block
     * @param string $template
     * @param string $header
     */
    public function addTab($alias, $title, $block, $template, $header = null)
    {
        if (!$title || !$block || !$template) {
            return;
        }

        $this->_tabs[] = array(
            'alias' => $alias,
            'title' => $title,
            'header' => $header,
        );

        $this->setChild($alias,
            $this->getLayout()->createBlock($block, $alias)
                ->setTemplate($template)
            );
    }

    /**
     * Return configured tabs
     *
     * @return array
     */
    public function getTabs()
    {
        return $this->_tabs;
    }
}
