<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Css page block
 */
class Magento_Page_Block_Html_Head_Css extends Magento_Core_Block_Abstract
    implements Magento_Page_Block_Html_Head_AssetBlock
{
    /**
     * Contructor
     *
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Page_Asset_ViewFileFactory $viewFileFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Page_Asset_ViewFileFactory $viewFileFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);

        $this->setAsset(
            $viewFileFactory->create(array(
                'file' => (string)$this->getFile(),
                'contentType' => Magento_Core_Model_View_Publisher::CONTENT_TYPE_CSS
            ))
        );
    }

    /**
     * Get block asset
     *
     * @return Magento_Core_Model_Page_Asset_AssetInterface
     */
    public function getAsset()
    {
        return $this->_getData('asset');
    }
}
