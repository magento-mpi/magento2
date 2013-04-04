<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Saas_Launcher
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
require_once 'Saas/Launcher/controllers/Adminhtml/Promotestore/IndexController.php';

class Saas_Launcher_Adminhtml_Promotestore_IndexControllerTest
    extends Saas_Launcher_Controller_BasePageTestCaseAbstract
{
    /**
     * Retrieve mocked page controller instance
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param string|null $areaCode
     * @param array $invokeArgs
     * @return Saas_Launcher_Controller_BasePage
     */
    protected function _getMockedPageControllerInstance(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        return $this->getMock(
            'Saas_Launcher_Adminhtml_Promotestore_IndexController',
            array(
                'loadLayout',
                'getLayout',
                'renderLayout',
                '_setActiveMenu',
            ),
            array($request, $response, $objectManager, $frontController, $layoutFactory, $areaCode, $invokeArgs)
        );
    }
}
