<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Category form image field helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Category\Helper;

class Image extends \Magento\Data\Form\Element\Image
{
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = \Mage::getBaseUrl('media').'catalog/category/'. $this->getValue();
        }
        return $url;
    }
}
