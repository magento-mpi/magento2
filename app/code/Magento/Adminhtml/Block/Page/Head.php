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
 * Adminhtml header block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Page_Head extends Magento_Page_Block_Html_Head
{
    /**
     * @var string
     */
    protected $_template = 'page/head.phtml';

    /**
     * @var Magento_Core_Model_Session
     */
    protected $_session;

    /**
     * @param Magento_Core_Model_Session $session
     * @param Magento_Core_Helper_File_Storage_Database $fileStorageDatabase
     * @param Magento_Core_Helper_Data $coreData
     * @param \Magento_Core_Block_Template_Context $context
     * @param \Magento_ObjectManager $objectManager
     * @param \Magento_Core_Model_Page $page
     * @param \Magento_Core_Model_Page_Asset_MergeService $assetMergeService
     * @param \Magento_Core_Model_Page_Asset_MinifyService $assetMinifyService
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Session $session,
        Magento_Core_Helper_File_Storage_Database $fileStorageDatabase,
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_ObjectManager $objectManager,
        Magento_Core_Model_Page $page,
        Magento_Core_Model_Page_Asset_MergeService $assetMergeService,
        Magento_Core_Model_Page_Asset_MinifyService $assetMinifyService,
        array $data = array()
    ) {
        $this->_session = $session;
        parent::__construct(
            $fileStorageDatabase, $coreData, $context, $objectManager, $page,
            $assetMergeService, $assetMinifyService, $data
        );
    }

    /**
     * Retrieve Session Form Key
     *
     * @return string
     */
    public function getFormKey()
    {
        return $this->_session->getFormKey();
    }
}
