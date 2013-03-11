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
 * Hub controller
 *
 * @category    Mage
 * @package     Mage_Launcher
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Launcher_Adminhtml_HubController extends Mage_Backend_Controller_ActionAbstract
{
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
     * @param Mage_Launcher_Helper_Data $launcherHelper
     * @param null $areaCode
     * @param array $invokeArgs
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Launcher_Helper_Data $launcherHelper,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        parent::__construct(
            $request, $response, $objectManager, $frontController, $layoutFactory, $areaCode, $invokeArgs
        );
        $this->_launcherHelper = $launcherHelper;
    }

    /**
     * Index action
     */
    public function indexAction()
    {
        if ($this->_launcherHelper->getLauncherPhase() == Mage_Launcher_Helper_Data::LAUNCHER_PHASE_PROMOTE_STORE) {
            $this->_redirect('*/promotestore_index/index');
        } else {
            $this->_redirect('*/storelauncher_index/index');
        }
    }
}
