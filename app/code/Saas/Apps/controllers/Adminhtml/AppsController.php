<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Apps
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml apps controller
 *
 * @category    Saas
 * @package     Saas_Saas
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Saas_Apps_Adminhtml_AppsController extends Mage_Backend_Controller_ActionAbstract
{
    /**
     * Apps helper
     *
     * @var Saas_Apps_Helper_Data
     */
    protected $_appsHelper;

    /**
     * Apps model
     *
     * @var Saas_Apps_Model_Adminhtml_App
     */
    protected $_appsModel;

    /**
     * Apps backend controller constructor
     *
     * @param Saas_Apps_Helper_Data $helper
     * @param Mage_Backend_Controller_Context $context
     * @param Saas_Apps_Model_Adminhtml_App $appsModel
     */
    public function __construct(
        Mage_Backend_Controller_Context $context,
        Saas_Apps_Helper_Data $helper,
        Saas_Apps_Model_Adminhtml_App $appsModel
    ) {
        $this->_appsHelper = $helper;
        $this->_appsModel = $appsModel;
        parent::__construct($context, Mage_Backend_Helper_Data::BACKEND_AREA_CODE);
    }

    /**
     * Show external applications tab
     *
     * @return void
     */
    public function indexAction()
    {
        $this->loadLayout();
        $this->_title($this->_appsHelper->__('Add - ons'));
        $this->_setActiveMenu('Saas_Apps::apps');
        $this->renderLayout();
    }

    /**
     * Create page on our domain with external content
     *
     * @return void
     */
    public function iFrameProxyPageAction()
    {
        $this->getResponse()->setBody($this->_appsModel->getContents());
    }
}