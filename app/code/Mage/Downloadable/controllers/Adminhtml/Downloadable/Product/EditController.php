<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';

/**
 * Adminhtml downloadable product edit
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Adminhtml_Downloadable_Product_EditController extends Mage_Adminhtml_Catalog_ProductController
{

    /**
     * Varien class constructor
     *
     */
    protected function _construct()
    {
        $this->setUsedModuleName('Mage_Downloadable');
    }

    /**
     * Load downloadable tab fieldsets
     *
     */
    public function formAction()
    {
        $this->_initProduct();
        $this->getResponse()->setBody(
            $this->getLayout()
                ->createBlock(
                     'Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable',
                    'admin.product.downloadable.information')
                ->toHtml()
        );
    }

    /**
     * Download process
     *
     * @param string $resource
     * @param string $resourceType
     */
    protected function _processDownload($resource, $resourceType)
    {
        $helper = Mage::helper('Mage_Downloadable_Helper_Download');
        /* @var $helper Mage_Downloadable_Helper_Download */

        $helper->setResource($resource, $resourceType);

        $fileName       = $helper->getFilename();
        $contentType    = $helper->getContentType();

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);

        if ($fileSize = $helper->getFilesize()) {
            $this->getResponse()
                ->setHeader('Content-Length', $fileSize);
        }

        if ($contentDisposition = $helper->getContentDisposition()) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
        }

        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

        $helper->output();
    }

    /**
     * Download link action
     *
     */
    public function linkAction()
    {
        $linkId = $this->getRequest()->getParam('id', 0);
        $link = Mage::getModel('Mage_Downloadable_Model_Link')->load($linkId);
        if ($link->getId()) {
            $resource = '';
            $resourceType = '';
            if ($link->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $link->getLinkUrl();
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($link->getLinkType() == Mage_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('Mage_Downloadable_Helper_File')->getFilePath(
                    Mage_Downloadable_Model_Link::getBasePath(), $link->getLinkFile()
                );
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
            } catch (Mage_Core_Exception $e) {
                $this->_getCustomerSession()->addError(Mage::helper('Mage_Downloadable_Helper_Data')->__('Something went wrong while getting the requested content.'));
            }
        }
        exit(0);
    }

}
