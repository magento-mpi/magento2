<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GiftWrapping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Gift Wrapping Image Helper
 *
 * @category   Magento
 * @package    Magento_GiftWrapping
 */
namespace Magento\GiftWrapping\Block\Adminhtml\Giftwrapping\Helper;

class Image extends \Magento\Data\Form\Element\Image
{
    /**
     * Get gift wrapping image url
     *
     * @return string|boolean
     */
    protected function _getUrl()
    {
        $url = false;
        if ($this->getValue()) {
            $url = $this->getForm()->getDataObject()->getImageUrl();
        }
        return $url;
    }

    /**
     * Get default field name
     *
     * @return string
     */
    public function getDefaultName()
    {
        $name = $this->getData('name');
        $suffix = $this->getForm()->getFieldNameSuffix();
        if ($suffix) {
            $name = $this->getForm()->addSuffixToName($name, $suffix);
        }
        return $name;
    }

}
