<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml header block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Page_Head extends Mage_Page_Block_Html_Head
{
    protected $_template = 'page/head.phtml';

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Page_Asset_MergeService $assetMergeService
     * @param Mage_Page_Model_GroupedAssets $assets
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Page_Asset_MergeService $assetMergeService,
        Mage_Page_Model_GroupedAssets $assets,
        array $data = array()
    ) {
        parent::__construct($context, $objectManager,
            $assetMergeService, $assets, $data
        );
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return Mage::getSingleton('Mage_Core_Model_Session')->getFormKey();
    }
}
