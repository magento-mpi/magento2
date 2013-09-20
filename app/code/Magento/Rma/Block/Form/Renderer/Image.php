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
 */
class Magento_Rma_Block_Form_Renderer_Image extends Magento_CustomAttribute_Block_Form_Renderer_Image
{
    /**
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        array $data = array()
    ) {
        $this->_storeManager = $storeManager;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Gets image url path
     *
     * @return string
     */
    public function getImageUrl()
    {
        $url = $this->_storeManager->getStore()->getBaseUrl('media') . Magento_Rma_Model_Item::ITEM_IMAGE_URL;
        $file = $this->getValue();
        $url = $url . $file;
        return $url;
    }
}
