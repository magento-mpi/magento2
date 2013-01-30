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
class Mage_Launcher_Adminhtml_Storelauncher_IndexController extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Core Config Model
     *
     * @var Mage_Core_Model_Config
     */
    protected $_configModel;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param string $areaCode
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Core_Model_Config $configModel
     * @param array $invokeArgs
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        $areaCode = null,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Core_Model_Config $configModel,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $areaCode, $objectManager, $frontController,
            $layoutFactory, $invokeArgs
        );
        $this->_configModel = $configModel;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        $layout = $this->loadLayout();
        $layout->getLayout();
        $layout->renderLayout();
    }

    public function launchAction()
    {
        $this->_configModel->saveConfig('design/head/demonotice', 0);
        $responseContent = Mage::helper('Mage_Launcher_Helper_Data')->jsonEncode(array(
            'success' => true,
        ));
        $this->getResponse()->setBody($responseContent);
    }
}
