<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer reviews controller
 *
 * @category   Mage
 * @package    Mage_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Rss_Controller_Catalog extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Mage_Core_Model_Config_Scope
     */
    protected $_configScope;

    /**
     * @param Mage_Core_Controller_Varien_Action_Context $context
     * @param Mage_Core_Model_Config_Scope $configScope
     */
    public function __construct(
        Mage_Core_Controller_Varien_Action_Context $context,
        Mage_Core_Model_Config_Scope $configScope
    ) {
        $this->_configScope = $configScope;
        parent::__construct($context);
    }

    /**
     * Emulate admin area for certain actions
     */
    public function preDispatch()
    {
        $action = $this->getRequest()->getActionName();
        /**
         * Format actionName => acrResourceId
         */
        $acl = array('notifystock' => 'Mage_Catalog::products', 'review' => 'Mage_Review::reviews_all');
        if (isset($acl[$action])) {
            $this->_configScope->setCurrentScope(Mage_Core_Model_App_Area::AREA_ADMINHTML);
            if (Mage_Rss_Controller_Order::authenticateAndAuthorizeAdmin($this, $acl[$action])) {
                return;
            }
        }
        parent::preDispatch();
    }

    public function newAction()
    {
        $this->_genericAction('new');
    }

    public function specialAction()
    {
        $this->_genericAction('special');
    }

    public function salesruleAction()
    {
        $this->_genericAction('salesrule');
    }

    public function notifystockAction()
    {
        $this->_render();
    }

    public function reviewAction()
    {
        $this->_render();
    }

    public function categoryAction()
    {
         $this->_genericAction('category');
    }

    /**
     * Render or forward to "no route" action if this type of RSS is disabled
     *
     * @param string $code
     */
    protected function _genericAction($code)
    {
        if ($this->_isEnabled($code)) {
            $this->_render();
        } else {
            $this->_forward('nofeed', 'index', 'rss');
        }
    }

    /**
     * Whether specified type of RSS is enabled
     *
     * @param string $code
     * @return bool
     */
    protected function _isEnabled($code)
    {
        return Mage::getStoreConfigFlag("rss/catalog/{$code}");
    }

    /**
     * Render as XML-document using layout handle without inheriting any other handles
     */
    protected function _render()
    {
        $this->getResponse()->setHeader('Content-Type', 'text/xml; charset=UTF-8');
        $this->loadLayout(false);
        $this->renderLayout();
    }
}
