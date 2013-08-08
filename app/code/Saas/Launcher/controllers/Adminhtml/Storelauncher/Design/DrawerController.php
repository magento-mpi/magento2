<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * BusinessInfo Drawer controller
 *
 * @category    Mage
 * @package     Saas_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Launcher_Adminhtml_Storelauncher_Design_DrawerController
    extends Saas_Launcher_Controller_BaseDrawer
{
    /**
     * Launcher Helper
     *
     * @var Saas_Launcher_Helper_Data
     */
    protected  $_helperFactory;

    /**
     * @param Mage_Backend_Controller_Context $context
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param string $areaCode
     * @param array $data
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Mage_Core_Model_Factory_Helper $helperFactory,
        $areaCode = null,
        array $data = array()
    ) {
        parent::__construct($context, $areaCode, $data);
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
            $imageAdapter = $this->_objectManager->get('Mage_Core_Model_Image_AdapterFactory')->create();
            $uploader->addValidateCallback('store_logo_image', $imageAdapter, 'validateUploadFile');
            $uploader->setAllowRenameFiles(true);

            /** @var $helper Saas_Launcher_Helper_Data */
            $helper = $this->_helperFactory->get('Saas_Launcher_Helper_Data');

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

        $this->getResponse()->setBody($this->_helperFactory->get('Saas_Launcher_Helper_Data')->jsonEncode($result));
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
                throw new Saas_Launcher_Exception('Logo caption must not be empty.');
            }
            /** @var $launcherHelper Saas_Launcher_Helper_Data */
            $launcherHelper = $this->_helperFactory->get('Saas_Launcher_Helper_Data');

            $logoImage = $this->_objectManager->get('Mage_Core_Model_Image_Factory')->create();
            $logoImage->createPngFromString($logoCaption,
                Mage::getBaseDir() . '/lib/LinLibertineFont/LinLibertine_Re-4.4.1.ttf');
            $logoImage->save($launcherHelper->getTmpLogoPath(), Saas_Launcher_Helper_Data::GENERATED_LOGO_NAME);

            $responseData = array(
                'url' => $launcherHelper->getTmpLogoUrl(Saas_Launcher_Helper_Data::GENERATED_LOGO_NAME),
                'file' => Saas_Launcher_Helper_Data::GENERATED_LOGO_NAME . '.tmp',
            );
            $responseContent = $this->_composeAjaxResponseContent('', true, $responseData);
        } catch (Exception $exception) {
            $responseContent = $this->_composeAjaxResponseContent(__($exception->getMessage()), false, array(
                'errorcode' => $exception->getCode()
            ));
        }
        $this->getResponse()->setBody($responseContent);
    }
}
