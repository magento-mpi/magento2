<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer;

/**
 * Integration Name Renderer
 */
class Name extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * Render integration name.
     *
     * If integration endpoint URL is unsecure then add error message to integration name.
     *
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function render(\Magento\Framework\Object $row)
    {
        /** @var \Magento\Integration\Model\Integration $row */
        $text = parent::render($row);
        if (!$this->isUrlSecure($row->getEndpoint()) || !$this->isUrlSecure($row->getIdentityLinkUrl())) {
            $text .= '<span class="icon-error"><span>Integration not secure</span></span>';
        }
        return $text;
    }

    /**
     * Check if URL is secure.
     *
     * @param string $url
     * @return bool
     */
    protected function isUrlSecure($url)
    {
        return (strpos($url, 'http:') !== 0);
    }
}
