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
class Saas_Launcher_Controller_Adminhtml_Promotestore_IndexTest
    extends Saas_Launcher_Controller_BasePageTestCaseAbstract
{
    /**
     * Retrieve mocked page controller instance
     *
     * @param Magento_Core_Controller_Request_Http $request
     * @param Magento_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Magento_Core_Controller_Varien_Front $frontController
     * @param Magento_Core_Model_Layout_Factory $layoutFactory
     * @param string|null $areaCode
     * @return Saas_Launcher_Controller_BasePage
     */
    protected function _getMockedPageControllerInstance(
        Magento_Core_Controller_Request_Http $request,
        Magento_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Magento_Core_Controller_Varien_Front $frontController,
        Magento_Core_Model_Layout_Factory $layoutFactory,
        $areaCode = null
    ) {
        $helper = new Magento_Test_Helper_ObjectManager($this);
        $arguments = array(
            'request' => $request,
            'response' => $response,
            'objectManager' => $objectManager,
            'frontController' => $frontController,
            'layoutFactory' => $layoutFactory,
        );
        $context = $helper->getObject('Mage_Backend_Controller_Context', $arguments);
        return $this->getMock(
            'Saas_Launcher_Controller_Adminhtml_Promotestore_Index',
            array(
                'loadLayout',
                'getLayout',
                'renderLayout',
                '_setActiveMenu',
            ),
            array($context, $areaCode)
        );
    }
}
