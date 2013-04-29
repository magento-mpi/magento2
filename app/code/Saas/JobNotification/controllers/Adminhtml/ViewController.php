<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Saas_JobNotification_Adminhtml_ViewController extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * @var Mage_Core_Model_Authorization
     */
    protected $_authorization;

    /**
     * @param Mage_Core_Controller_Request_Http $request
     * @param Mage_Core_Controller_Response_Http $response
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Controller_Varien_Front $frontController
     * @param Mage_Core_Model_Layout_Factory $layoutFactory
     * @param Mage_Core_Model_Authorization $authorization
     * @param string $areaCode
     * @param array $invokeArgs
     */
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Controller_Response_Http $response,
        Magento_ObjectManager $objectManager,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Layout_Factory $layoutFactory,
        Mage_Core_Model_Authorization $authorization,
        $areaCode = null,
        array $invokeArgs = array()
    ) {
        parent::__construct($request, $response, $objectManager,
            $frontController, $layoutFactory, $areaCode, $invokeArgs
        );
        $this->_authorization = $authorization;
    }

    /**
     * Check whether controller actions is allowed
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Saas_JobNotification::notification_grid');
    }

    /**
     * Index grid view action
     */
    public function indexAction()
    {
        $this->loadLayout();

        $this->_setActiveMenu('Saas_JobNotification::grid');
        $this->_title('Tasks Notifications');

        $this->renderLayout();
    }

    /**
     * Ajax grid view action
     */
    public function gridAction()
    {
        $this->loadLayout(false);
        $this->renderLayout();
    }
}