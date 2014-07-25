<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Label & link block
 *
 * @method string getLabel()
 * @method string getItemUrl()
 * @method string getItemName()
 */
namespace Magento\UrlRedirect\Block;

class Link extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Render output
     *
     * @return string
     */
    protected function _toHtml()
    {
        return '<p>' . $this->getLabel() . ' <a href="' . $this->getItemUrl() . '">' . $this->escapeHtml(
            $this->getItemName()
        ) . '</a></p>';
    }
}
