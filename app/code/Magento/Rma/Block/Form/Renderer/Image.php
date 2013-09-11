<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rma Item Form Renderer Block for select
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Form\Renderer;

class Image extends \Magento\CustomAttribute\Block\Form\Renderer\Image
{

    /**
     * Gets image url path
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = \Mage::getBaseUrl('media')
            .\Magento\Rma\Model\Item::ITEM_IMAGE_URL;


        $file = $this->getValue();
        if(substr($file, 0, 1) == '/') {
            $file = $file;
        }
        $url = $url.$file;

        return $url;
    }


}
