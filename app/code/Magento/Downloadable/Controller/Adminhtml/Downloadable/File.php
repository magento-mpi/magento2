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
 * Downloadable File upload controller
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Downloadable_Controller_Adminhtml_Downloadable_File extends Magento_Adminhtml_Controller_Action
{
    /**
     * @var Magento_Downloadable_Model_Link
     */
    protected $_link;

    /**
     * @var Magento_Downloadable_Model_Sample
     */
    protected $_sample;

    /**
     * @param Magento_Backend_Controller_Context $context
     * @param Magento_Downloadable_Model_Link $link
     * @param Magento_Downloadable_Model_Sample $sample
     */
    public function __construct(
        Magento_Backend_Controller_Context $context,
        Magento_Downloadable_Model_Link $link,
        Magento_Downloadable_Model_Sample $sample
    ) {
        $this->_link = $link;
        $this->_sample = $sample;
        parent::__construct($context);
    }

    /**
     * Upload file controller action
     */
    public function uploadAction()
    {
        $type = $this->getRequest()->getParam('type');
        $tmpPath = '';
        if ($type == 'samples') {
            $tmpPath = $this->_sample->getBaseTmpPath();
        } elseif ($type == 'links') {
            $tmpPath = $this->_link->getBaseTmpPath();
        } elseif ($type == 'link_samples') {
            $tmpPath = $this->_link->getBaseSampleTmpPath();
        }
        $result = array();
        try {
            $uploader = $this->_objectManager->create('Magento_Core_Model_File_Uploader', array('type' => $type));
            $uploader->setAllowRenameFiles(true);
            $uploader->setFilesDispersion(true);
            $result = $uploader->save($tmpPath);

            /**
             * Workaround for prototype 1.7 methods "isJSON", "evalJSON" on Windows OS
             */
            $result['tmp_name'] = str_replace(DS, "/", $result['tmp_name']);
            $result['path'] = str_replace(DS, "/", $result['path']);

            if (isset($result['file'])) {
                $fullPath = rtrim($tmpPath, DS) . DS . ltrim($result['file'], DS);
                $this->_objectManager->get('Magento_Core_Helper_File_Storage_Database')->saveFile($fullPath);
            }

            $result['cookie'] = array(
                'name'     => session_name(),
                'value'    => $this->_getSession()->getSessionId(),
                'lifetime' => $this->_getSession()->getCookieLifetime(),
                'path'     => $this->_getSession()->getCookiePath(),
                'domain'   => $this->_getSession()->getCookieDomain()
            );
        } catch (Exception $e) {
            $result = array('error'=>$e->getMessage(), 'errorcode'=>$e->getCode());
        }

        $this->getResponse()->setBody($this->_objectManager->get('Magento_Core_Helper_Data')->jsonEncode($result));
    }

    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magento_Catalog::products');
    }
}
