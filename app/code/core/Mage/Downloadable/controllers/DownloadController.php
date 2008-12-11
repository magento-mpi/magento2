<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Download controller
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_DownloadController extends Mage_Core_Controller_Front_Action
{

    /**
     * Return core session object
     *
     * @return Mage_Core_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * Return customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function _processDownload($resource, $resourceType)
    {
        $helper = Mage::helper('downloadable/download');
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
        $this->getResponse()
            ->setHeader('Content-Disposition', 'attachment; filename='.$fileName)
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
        $sample = Mage::getModel('downloadable/sample')->load($sampleId);
        if ($sample->getId()) {
            $resource = $sample->getSampleUrl();
            $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_URL;
            if ($sample->getSampleFile()) {
                $resource = Mage_Downloadable_Model_Sample::getSampleDir() . '/' . $sample->getSampleFile();
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError(Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.'));
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
        $link = Mage::getModel('downloadable/link')->load($linkId);
        if ($link->getId()) {
            $resource = $link->getSampleUrl();
            $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_URL;
            if ($link->getSampleFile()) {
                $resource = Mage_Downloadable_Model_Link::getLinkDir() . '/' . $link->getSampleFile();
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
            } catch (Mage_Core_Exception $e) {
                $this->_getCustomerSession()->addError(Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.'));
            }
        }
        return $this->_redirectReferer();
    }

    /**
     * Download link action
     */
    public function linkAction()
    {
        $id = $this->getRequest()->getParam('id', 0);
        $linkPurchased = Mage::getModel('downloadable/link_purchased')->load($id);
        if (!$linkPurchased->getIsShareable()) {
            if (!$this->_getCustomerSession()->getCustomerId()) {
                $this->_getCustomerSession()->addNotice(Mage::helper('downloadable')->__('Please log in first.'));
                $this->_getCustomerSession()->authenticate($this);
                return ;
            }
        }
        $downloadsLeft = $linkPurchased->getNumberOfDownloadsBought() - $linkPurchased->getNumberOfDownloadsUsed();
        if ($linkPurchased->getStatus() == Mage_Downloadable_Model_Link_Purchased::LINK_STATUS_AVAILABLE
            && ($downloadsLeft || $linkPurchased->getNumberOfDownloadsBought() == 0)) {
            $resource = $linkPurchased->getLinkUrl();
            $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_URL;
            if ($linkPurchased->getLinkFile()) {
                $resource = Mage_Downloadable_Model_Link::getLinkDir() . '/' . $linkPurchased->getLinkFile();
                $resourceType = Mage_Downloadable_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                $linkPurchased->setNumberOfDownloadsUsed(
                    $linkPurchased->getNumberOfDownloadsUsed()+1
                );
                if ($linkPurchased->getNumberOfDownloadsBought() != 0
                    && !($linkPurchased->getNumberOfDownloadsBought() - $linkPurchased->getNumberOfDownloadsUsed())) {
                    $linkPurchased->setStatus(Mage_Downloadable_Model_Link_Purchased::LINK_STATUS_EXPIRED);
                }
                $linkPurchased->save();
            }
            catch (Exception $e) {
                $this->_getCustomerSession()->addError(Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.'));
            }
        } elseif ($linkPurchased->getStatus() == Mage_Downloadable_Model_Link_Purchased::LINK_STATUS_EXPIRED) {
            $this->_getCustomerSession()->addNotice(Mage::helper('downloadable')->__('Link is expiry.'));
        } elseif ($linkPurchased->getStatus() == Mage_Downloadable_Model_Link_Purchased::LINK_STATUS_PENDING) {
            $this->_getCustomerSession()->addNotice(Mage::helper('downloadable')->__('Link is not available.'));
        } else {
            $this->_getCustomerSession()->addError(Mage::helper('downloadable')->__('Sorry, the was an error getting requested content. Please contact store owner.'));
        }
        return $this->_redirect('*/customer/products');
    }

}
