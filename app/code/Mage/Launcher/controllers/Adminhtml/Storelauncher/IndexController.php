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
 * Launcher controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_Storelauncher_IndexController extends Mage_Launcher_Controller_BasePage
{
    /**
     * Core Config Model
     *
     * @var Mage_Core_Model_Config
     */
    protected $_configModel;

    /**
     * Config Writer Model
     *
     * @var Mage_Core_Model_Config_Storage_WriterInterface
     */
    protected $_configWriter;

    /**
     * Launcher Helper
     *
     * @var Mage_Launcher_Helper_Data
     */
    protected  $_launcherHelper;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Core_Model_Config $configModel
     * @param Mage_Core_Model_Config_Storage_WriterInterface $configWriter
     * @param Mage_Launcher_Helper_Data $launcherHelper,
     * @param string $areaCode
     * @param array $invokeArgs
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Core_Model_Config $configModel,
        Mage_Core_Model_Config_Storage_WriterInterface $configWriter,
        Mage_Launcher_Helper_Data $launcherHelper,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $objectManager, $frontController,
            $layoutFactory, $areaCode, $invokeArgs
        );
        $this->_configModel = $configModel;
        $this->_configWriter = $configWriter;
        $this->_launcherHelper = $launcherHelper;
    }

    /**
     * Launch store action
     */
    public function launchAction()
    {
        $responseContent = array();
        /** @var $page Mage_Launcher_Model_Page */
        $page = $this->_objectManager->create('Mage_Launcher_Model_Page')->loadByPageCode('store_launcher');
        if ($page->isComplete()) {
            $this->_configWriter->save('design/head/demonotice', 0);
            $this->_configWriter->save(
                Mage_Launcher_Helper_Data::CONFIG_PATH_LAUNCHER_PHASE,
                Mage_Launcher_Helper_Data::LAUNCHER_PHASE_PROMOTE_STORE
            );
            $this->_configModel->reinit();
            $responseContent = array(
                'success' => true,
            );
        } else {
            $responseContent = array(
                'success' => false,
                'error_message' => $this->_launcherHelper->__('All Tiles have to be completed before this action.')
            );
        }

        $this->getResponse()->setBody($this->_launcherHelper->jsonEncode($responseContent));
    }

    /**
     * Change state after showing Welcome Screen
     */
    public function showScreenAction()
    {
        $launcherFlag = $this->_objectManager->get('Mage_Launcher_Model_Storelauncher_Flag');
        $launcherFlag->loadSelf()->setState(1);
        $launcherFlag->save();
        $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
            'success' => true,
            'error_message' => '',
        ));
        $this->getResponse()->setBody($responseContent);
    }
}
