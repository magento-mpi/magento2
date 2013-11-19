<?php
/**
 * Renders HTML image.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Link;

use Magento\Integration\Block\Adminhtml\Widget\Grid\Column\Renderer\Link;
use Magento\Object;

class Image extends Link
{
    /**
     * Substitute text caption with an image.
     *
     * @return string
     */
    public function getCaption()
    {
        $imgSrc = $this->_getImgSrc();

        return $imgSrc ? sprintf('<img src="%s" alt="%s"></img>', $imgSrc, parent::getCaption()) : parent::getCaption();
    }

    /**
     * {@inheritDoc}
     */
    public function isDisabled()
    {
        return parent::isDisabled(); // @todo Need a mechanism to figure out if integration came from config file
    }

    /**
     * {@inheritDoc}
     */
    public function getAttributesHtml()
    {
        return sprintf('title="%s"', parent::getCaption());
    }

    /**
     * Get image source URL.
     *
     * @return string
     */
    protected function _getImgSrc()
    {
        return $this->getColumn()->getImgSrc();


        return $this->isDisabled($this->_row)
            ? $this->getColumn()->getDisabledImgSrc() ?: $this->getColumn()->getImgSrc()
            : $this->getColumn()->getImgSrc();
    }
}
