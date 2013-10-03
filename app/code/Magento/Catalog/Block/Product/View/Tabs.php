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
namespace Magento\Catalog\Block\Product\View;

class Tabs extends \Magento\Core\Block\Template
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
