<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * BusinessInfo Drawer controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_Storelauncher_Design_DrawerController
    extends Mage_Launcher_Controller_BaseDrawer
{
    /**
     * Launcher Helper
     *
     * @var Mage_Launcher_Helper_Data
     */
    protected  $_helperFactory;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param null $areaCode
     * @param array $data
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Core_Model_Factory_Helper $helperFactory,
        $areaCode = null,
        array $data = array()
    ) {
        parent::__construct($request, $response, $objectManager, $frontController, $layoutFactory, $areaCode, $data);
        $this->_helperFactory = $helperFactory;
    }

    /**
     * Upload Logo
     */
    public function uploadLogoAction()
    {
        try {
            /** @var $uploader Mage_Core_Model_File_Uploader */
            $uploader = $this->_objectManager->create('Mage_Core_Model_File_Uploader',
                array('fileId' => 'logo_upload'));
            $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
            $uploader->addValidateCallback('store_logo_image',
                $this->_helperFactory->get('Mage_Catalog_Helper_Image'), 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);

            /** @var $helper Mage_Launcher_Helper_Data */
            $helper = $this->_helperFactory->get('Mage_Launcher_Helper_Data');

            $result = $uploader->save($helper->getTmpLogoPath());

            unset($result['tmp_name']);
            unset($result['path']);

            $result['url'] = $helper->getTmpLogoUrl($result['file']);
            $result['file'] = $result['file'] . '.tmp';
            $result['success'] = true;
        } catch (Exception $e) {
            $result = array(
                'error' => $e->getMessage(),
                'errorcode' => $e->getCode()
            );
        }

        $this->getResponse()->setBody($this->_helperFactory->get('Mage_Launcher_Helper_Data')->jsonEncode($result));
    }

    /**
     * Generate logo based on provided string
     */
    public function generateLogoAction()
    {
        if (!$this->getRequest()->isPost()) {
            return;
        }
        $responseContent = '';
        try {
            $logoCaption = (string)$this->getRequest()->getPost('generated_logo_caption');
            if (empty($logoCaption)) {
                throw new Mage_Launcher_Exception('Logo caption must not be empty.');
            }
            /** @var $launcherHelper Mage_Launcher_Helper_Data */
            $launcherHelper = $this->_helperFactory->get('Mage_Launcher_Helper_Data');

            $imageAdapterType = $this->_helperFactory->get('Mage_Core_Helper_Data')->getImageAdapterType();
            $logoImage = new Varien_Image(null, $imageAdapterType);
            $logoImage->createPngFromString($logoCaption);
            $logoImage->save($launcherHelper->getTmpLogoPath(), Mage_Launcher_Helper_Data::GENERATED_LOGO_NAME);

            $responseData = array(
                'url' => $launcherHelper->getTmpLogoUrl(Mage_Launcher_Helper_Data::GENERATED_LOGO_NAME),
                'file' => Mage_Launcher_Helper_Data::GENERATED_LOGO_NAME . '.tmp',
            );
            $responseContent = $this->_composeAjaxResponseContent('', true, $responseData);
        } catch (Exception $exception) {
            $responseContent = $this->_composeAjaxResponseContent($this->__($exception->getMessage()), false, array(
                'errorcode' => $exception->getCode()
            ));
        }
        $this->getResponse()->setBody($responseContent);
    }
}
