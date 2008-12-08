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
     * Download sample action
     *
     */
    public function sampleAction()
    {
    }

    /**
     * Download link action
     *
     * @todo get link and type
     */
    public function linkAction()
    {
        // check access and etc

        $helper = Mage::helper('downloadable/download');
        /* @var $helper Mage_Downloadable_Helper_Download */

        /**
         * @todo next line
         */
        $helper->setResource('LINK_TO_FILE', Mage_Downloadable_Helper_Download::LINK_TYPE_URL);

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
     * Download link's sample action
     *
     */
    public function linkSampleAction()
    {
    }

}
