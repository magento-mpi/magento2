<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Cache;

class Additional extends \Magento\Backend\Block\Template
{
    /**
     * @return string
     */
    public function getCleanImagesUrl()
    {
        return $this->getUrl('*/*/cleanImages');
    }

    /**
     * @return string
     */
    public function getCleanMediaUrl()
    {
        return $this->getUrl('*/*/cleanMedia');
    }
}
