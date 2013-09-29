<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Adminhtml\Block\Cache;

class Additional extends \Magento\Adminhtml\Block\Template
{
    public function getCleanImagesUrl()
    {
        return $this->getUrl('*/*/cleanImages');
    }

    public function getCleanMediaUrl()
    {
        return $this->getUrl('*/*/cleanMedia');
    }
}
