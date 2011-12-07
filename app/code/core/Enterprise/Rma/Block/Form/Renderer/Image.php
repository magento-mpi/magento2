<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rma Item Form Renderer Block for select
 *
 * @category    Enterprise
 * @package     Enterprise_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Rma_Block_Form_Renderer_Image extends Enterprise_Eav_Block_Form_Renderer_Image
{

    /**
     * Gets image url path
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = Mage::getBaseUrl('media')
            .Enterprise_Rma_Model_Item::ITEM_IMAGE_URL;


        $file = $this->getValue();
        if(substr($file, 0, 1) == '/') {
            $file = $file;
        }
        $url = $url.$file;

        return $url;
    }


}
