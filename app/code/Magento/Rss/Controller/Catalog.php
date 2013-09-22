<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rss
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer reviews controller
 *
 * @category   Magento
 * @package    Magento_Rss
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rss\Controller;

class Catalog extends \Magento\Core\Controller\Front\Action
{
    /**
     * @var \Magento\Core\Model\Config\Scope
     */
    protected $_configScope;

    /**
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param \Magento\Core\Controller\Varien\Action\Context $context
     * @param \Magento\Core\Model\Config\Scope $configScope
     */
    public function __construct(
        \Magento\Core\Controller\Varien\Action\Context $context,
        \Magento\Core\Model\Config\Scope $configScope
    ) {
        $this->_configScope = $configScope;
        $this->_logger = $context->getLogger();
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
        $acl = array(
            'notifystock' => 'Magento_Catalog::products',
            'review' => 'Magento_Review::reviews_all',
        );
        if (isset($acl[$action])) {
            $this->_configScope->setCurrentScope(Magento_Core_Model_App_Area::AREA_ADMINHTML);
            if ($this->authenticateAndAuthorizeAdmin($acl[$action], $this->_logger)) {
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
        return $this->_objectManager->get('Magento\Core\Model\Store\Config')->getConfigFlag("rss/catalog/{$code}");
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
