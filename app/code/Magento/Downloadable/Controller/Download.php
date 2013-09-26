<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Download controller
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Controller_Download extends Magento_Core_Controller_Front_Action
{

    /**
     * Return core session object
     *
     * @return Magento_Core_Model_Session
     */
    protected function _getSession()
    {
        return $this->_objectManager->get('Magento_Core_Model_Session');
    }

    /**
     * Return customer session object
     *
     * @return Magento_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return $this->_objectManager->get('Magento_Customer_Model_Session');
    }

    protected function _processDownload($resource, $resourceType)
    {
        /* @var $helper Magento_Downloadable_Helper_Download */
        $helper = $this->_objectManager->get('Magento_Downloadable_Helper_Download');

        $helper->setResource($resource, $resourceType);
        $fileName = $helper->getFilename();
        $contentType = $helper->getContentType();

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
     * Download sample action
     *
     */
    public function sampleAction()
    {
        $sampleId = $this->getRequest()->getParam('sample_id', 0);
        /** @var Magento_Downloadable_Model_Sample $sample */
        $sample = $this->_objectManager->create('Magento_Downloadable_Model_Sample')->load($sampleId);
        if ($sample->getId()) {
            $resource = '';
            $resourceType = '';
            if ($sample->getSampleType() == Magento_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $sample->getSampleUrl();
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($sample->getSampleType() == Magento_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                /** @var Magento_Downloadable_Helper_File $helper */
                $helper = $this->_objectManager->get('Magento_Downloadable_Helper_File');
                $resource = $helper->getFilePath(
                    $sample->getBasePath(),
                    $sample->getSampleFile()
                );
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                exit(0);
            } catch (Magento_Core_Exception $e) {
                $this->_getSession()->addError(__('Sorry, there was an error getting requested content. Please contact the store owner.'));
            }
        }
        return $this->_redirectReferer();
    }

    /**
     * Download link's sample action
     *
     */
    public function linkSampleAction()
    {
        $linkId = $this->getRequest()->getParam('link_id', 0);
        $link = $this->_objectManager->create('Magento_Downloadable_Model_Link')->load($linkId);
        if ($link->getId()) {
            $resource = '';
            $resourceType = '';
            if ($link->getSampleType() == Magento_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $link->getSampleUrl();
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($link->getSampleType() == Magento_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = $this->_objectManager->get('Magento_Downloadable_Helper_File')->getFilePath(
                    $this->_getLink()->getBaseSamplePath(), $link->getSampleFile()
                );
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                exit(0);
            } catch (Magento_Core_Exception $e) {
                $this->_getCustomerSession()->addError(__('Sorry, there was an error getting requested content. Please contact the store owner.'));
            }
        }
        return $this->_redirectReferer();
    }

    /**
     * Download link action
     */
    public function linkAction()
    {
        $session = $this->_getCustomerSession();

        $id = $this->getRequest()->getParam('id', 0);
        /** @var Magento_Downloadable_Model_Link_Purchased_Item $linkPurchasedItem */
        $linkPurchasedItem = $this->_objectManager->create('Magento_Downloadable_Model_Link_Purchased_Item')
            ->load($id, 'link_hash');
        if (! $linkPurchasedItem->getId() ) {
            $session->addNotice(__("We can't find the link you requested."));
            return $this->_redirect('*/customer/products');
        }
        if (!$this->_objectManager->get('Magento_Downloadable_Helper_Data')->getIsShareable($linkPurchasedItem)) {
            $customerId = $session->getCustomerId();
            if (!$customerId) {
                /** @var Magento_Catalog_Model_Product $product */
                $product = $this->_objectManager->create('Magento_Catalog_Model_Product')
                    ->load($linkPurchasedItem->getProductId());
                if ($product->getId()) {
                    $notice = __('Please log in to download your product or purchase <a href="%1">%2</a>.',
                        $product->getProductUrl(),
                        $product->getName()
                    );
                } else {
                    $notice = __('Please log in to download your product.');
                }
                $session->addNotice($notice);
                $session->authenticate($this);
                $session->setBeforeAuthUrl(
                    $this->_objectManager->create('Magento_Core_Model_Url')->getUrl(
                        'downloadable/customer/products/',
                        array('_secure' => true)
                    )
                );
                return ;
            }
            /** @var Magento_Downloadable_Model_Link_Purchased $linkPurchased */
            $linkPurchased = $this->_objectManager->create('Magento_Downloadable_Model_Link_Purchased')
                ->load($linkPurchasedItem->getPurchasedId());
            if ($linkPurchased->getCustomerId() != $customerId) {
                $session->addNotice(__("We can't find the link you requested."));
                return $this->_redirect('*/customer/products');
            }
        }
        $downloadsLeft = $linkPurchasedItem->getNumberOfDownloadsBought()
            - $linkPurchasedItem->getNumberOfDownloadsUsed();

        $status = $linkPurchasedItem->getStatus();
        if ($status == Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_AVAILABLE
            && ($downloadsLeft || $linkPurchasedItem->getNumberOfDownloadsBought() == 0)
        ) {
            $resource = '';
            $resourceType = '';
            if ($linkPurchasedItem->getLinkType() == Magento_Downloadable_Helper_Download::LINK_TYPE_URL) {
                $resource = $linkPurchasedItem->getLinkUrl();
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_URL;
            } elseif ($linkPurchasedItem->getLinkType() == Magento_Downloadable_Helper_Download::LINK_TYPE_FILE) {
                $resource = $this->_objectManager->get('Magento_Downloadable_Helper_File')->getFilePath(
                    $this->_getLink()->getBasePath(),
                    $linkPurchasedItem->getLinkFile()
                );
                $resourceType = Magento_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                $linkPurchasedItem->setNumberOfDownloadsUsed($linkPurchasedItem->getNumberOfDownloadsUsed() + 1);

                if ($linkPurchasedItem->getNumberOfDownloadsBought() != 0 && !($downloadsLeft - 1)) {
                    $linkPurchasedItem->setStatus(Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED);
                }
                $linkPurchasedItem->save();
                exit(0);
            }
            catch (Exception $e) {
                $session->addError(
                    __('Something went wrong while getting the requested content.')
                );
            }
        } elseif ($status == Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_EXPIRED) {
            $session->addNotice(__('The link has expired.'));
        } elseif ($status == Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PENDING
            || $status == Magento_Downloadable_Model_Link_Purchased_Item::LINK_STATUS_PAYMENT_REVIEW
        ) {
            $session->addNotice(__('The link is not available.'));
        } else {
            $session->addError(
                __('Something went wrong while getting the requested content.')
            );
        }
        return $this->_redirect('*/customer/products');
    }

    /**
     * @return Magento_Downloadable_Model_Link
     */
    protected function _getLink()
    {
        return $this->_objectManager->get('Magento_Downloadable_Model_Link');
    }
}
