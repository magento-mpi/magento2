<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Page
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Script page block
 *
 * @category   Mage
 * @package    Mage_Page
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Page_Block_Html_Head_Script extends Mage_Core_Block_Abstract implements Mage_Page_Block_Html_Head_AssetBlock
{
    /**
     * Contructor
     *
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_Page_Asset_ViewFileFactory $viewFileFactory
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_Page_Asset_ViewFileFactory $viewFileFactory,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->setAsset(
            $viewFileFactory->create(array(
                'file' => (string)$this->getFile(),
                'contentType' => Mage_Core_Model_View_Publisher::CONTENT_TYPE_JS
            ))
        );
    }

    /**
     * Get block asset
     *
     * @return Mage_Core_Model_Page_Asset_AssetInterface
     */
    public function getAsset()
    {
        return $this->getData('asset');
    }
}
