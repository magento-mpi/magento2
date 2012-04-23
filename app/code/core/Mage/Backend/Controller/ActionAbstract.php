<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


abstract class Mage_Backend_Controller_ActionAbstract extends Mage_Core_Controller_Varien_Action
{
    /**
     * Name of "is URLs checked" flag
     */
    const FLAG_IS_URLS_CHECKED = 'check_url_settings';

    /**
     * Session namespace to refer in other places
     */
    const SESSION_NAMESPACE = 'adminhtml';

    /**
     * Array of actions which can be processed without secret key validation
     *
     * @var array
     */
    protected $_publicActions = array();

    /**
     * Namespace for session.
     *
     * @var string
     */
    protected $_sessionNamespace = self::SESSION_NAMESPACE;

    protected function _isAllowed()
    {
        return true;
    }

    /**
     * Retrieve adminhtml session model object
     *
     * @return Mage_Backend_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('Mage_Backend_Model_Session');
    }

    /**
     * Retrieve base admihtml helper
     *
     * @return Mage_Backend_Helper_Data
     */
    protected function _getHelper()
    {
        return Mage::helper('Mage_Backend_Helper_Data');
    }

    /**
     * Define active menu item in menu block
     * @param $menuPath
     * @return Mage_Backend_Controller_ActionAbstract
     */
    protected function _setActiveMenu($menuPath)
    {
        $this->getLayout()->getBlock('menu')->setActive($menuPath);
        return $this;
    }

    /**
     * @param $label
     * @param $title
     * @param null $link
     * @return Mage_Backend_Controller_ActionAbstract
     */
    protected function _addBreadcrumb($label, $title, $link=null)
    {
        $this->getLayout()->getBlock('breadcrumbs')->addLink($label, $title, $link);
        return $this;
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     * @return Mage_Backend_Controller_ActionAbstract
     */
    protected function _addContent(Mage_Core_Block_Abstract $block)
    {
        return $this->_moveBlockToContainer($block, 'content');
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     * @return Mage_Backend_Controller_ActionAbstract
     */
    protected function _addLeft(Mage_Core_Block_Abstract $block)
    {
        return $this->_moveBlockToContainer($block, 'left');
    }

    /**
     * @param Mage_Core_Block_Abstract $block
     * @return Mage_Backend_Controller_ActionAbstract
     */
    protected function _addJs(Mage_Core_Block_Abstract $block)
    {
        return $this->_moveBlockToContainer($block, 'js');
    }

    /**
     * Set specified block as an anonymous child to specified container
     *
     * The block will be moved to the container from previous parent after all other elements
     *
     * @param Mage_Core_Block_Abstract $block
     * @param string $containerName
     * @return Mage_Backend_Controller_ActionAbstract
     */
    private function _moveBlockToContainer(Mage_Core_Block_Abstract $block, $containerName)
    {
        $this->getLayout()->setChild($containerName, $block->getNameInLayout(), '');
        return $this;
    }

    /**
     * Controller predispatch method
     *
     * @return Mage_Backend_Controller_ActionAbstract
     */
    public function preDispatch()
    {
        Mage::app()->setCurrentStore('admin');
        $this->_areaDesign = (string)Mage::getConfig()->getNode(
            $this->_currentArea . '/' . Mage_Core_Model_Design_Package::XML_PATH_THEME
        ) ?: 'default/default/default'; // always override frontend theme

        Mage::dispatchEvent('adminhtml_controller_action_predispatch_start', array());
        parent::preDispatch();
        $_isValidFormKey = true;
        $_isValidSecretKey = true;
        $_keyErrorMsg = '';
        if (Mage::getSingleton('Mage_Admin_Model_Session')->isLoggedIn()) {
            if ($this->getRequest()->isPost()) {
                $_isValidFormKey = $this->_validateFormKey();
                $_keyErrorMsg = Mage::helper('Mage_Backend_Helper_Data')->__('Invalid Form Key. Please refresh the page.');
            } elseif (Mage::getSingleton('Mage_Backend_Model_Url')->useSecretKey()) {
                $_isValidSecretKey = $this->_validateSecretKey();
                $_keyErrorMsg = Mage::helper('Mage_Backend_Helper_Data')->__('Invalid Secret Key. Please refresh the page.');
            }
        }
        if (!$_isValidFormKey || !$_isValidSecretKey) {
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            $this->setFlag('', self::FLAG_NO_POST_DISPATCH, true);
            if ($this->getRequest()->getQuery('isAjax', false) || $this->getRequest()->getQuery('ajax', false)) {
                $this->getResponse()->setBody(Mage::helper('Mage_Core_Helper_Data')->jsonEncode(array(
                    'error' => true,
                    'message' => $_keyErrorMsg
                )));
            } else {
                $this->_redirect( Mage::getSingleton('Mage_Admin_Model_Session')->getUser()->getStartupPageUrl() );
            }
            return $this;
        }

        if ($this->getRequest()->isDispatched()
            && $this->getRequest()->getActionName() !== 'denied'
            && !$this->_isAllowed()) {
            $this->_forward('denied');
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
            return $this;
        }

        if (!$this->getFlag('', self::FLAG_IS_URLS_CHECKED)
            && !$this->getRequest()->getParam('forwarded')
            && !$this->_getSession()->getIsUrlNotice(true)
            && !Mage::getConfig()->getNode('global/can_use_base_url')) {
            $this->setFlag('', self::FLAG_IS_URLS_CHECKED, true);
        }
        if (is_null(Mage::getSingleton('Mage_Backend_Model_Session')->getLocale())) {
            Mage::getSingleton('Mage_Backend_Model_Session')->setLocale(Mage::app()->getLocale()->getLocaleCode());
        }

        return $this;
    }

    public function deniedAction()
    {
        $this->getResponse()->setHeader('HTTP/1.1','403 Forbidden');
        if (!Mage::getSingleton('Mage_Admin_Model_Session')->isLoggedIn()) {
            $this->_redirect('*/index/login');
            return;
        }
        $this->loadLayout(array('default', 'adminhtml_denied'));
        $this->renderLayout();
    }

    public function loadLayout($ids=null, $generateBlocks=true, $generateXml=true)
    {
        parent::loadLayout($ids, $generateBlocks, $generateXml);
        $this->_initLayoutMessages('Mage_Backend_Model_Session');
        return $this;
    }

    public function norouteAction($coreRoute = null)
    {
        $this->getResponse()->setHeader('HTTP/1.1','404 Not Found');
        $this->getResponse()->setHeader('Status','404 File not found');
        $this->loadLayout(array('default', 'adminhtml_noroute'));
        $this->renderLayout();
    }

    /**
     * Set referer url for redirect in responce
     *
     * Is overriden here to set defaultUrl to admin url
     *
     * @param   string $defaultUrl
     * @return Mage_Backend_Controller_ActionAbstract
     */
    protected function _redirectReferer($defaultUrl=null)
    {
        $defaultUrl = empty($defaultUrl) ? $this->getUrl('*') : $defaultUrl;
        parent::_redirectReferer($defaultUrl);
        return $this;
    }

    /**
     * Set redirect into responce
     *
     * @param   string $path
     * @param   array $arguments
     * @return Mage_Backend_Controller_ActionAbstract
     */
    protected function _redirect($path, $arguments=array())
    {
        $this->_getSession()->setIsUrlNotice($this->getFlag('', self::FLAG_IS_URLS_CHECKED));
        $this->getResponse()->setRedirect($this->getUrl($path, $arguments));
        return $this;
    }

    protected function _forward($action, $controller = null, $module = null, array $params = null)
    {
        $this->_getSession()->setIsUrlNotice($this->getFlag('', self::FLAG_IS_URLS_CHECKED));
        return parent::_forward($action, $controller, $module, $params);
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route='', $params=array())
    {
        return Mage::helper('Mage_Backend_Helper_Data')->getUrl($route, $params);
    }

    /**
     * Validate Secret Key
     *
     * @return bool
     */
    protected function _validateSecretKey()
    {
        if (is_array($this->_publicActions) && in_array($this->getRequest()->getActionName(), $this->_publicActions)) {
            return true;
        }

        if (!($secretKey = $this->getRequest()->getParam(Mage_Backend_Model_Url::SECRET_KEY_PARAM_NAME, null))
            || $secretKey != Mage::getSingleton('Mage_Backend_Model_Url')->getSecretKey()) {
            return false;
        }
        return true;
    }

    /**
     * Translate a phrase
     *
     * @return string
     */
    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), $this->_getRealModuleName());
        array_unshift($args, $expr);
        return Mage::app()->getTranslator()->translate($args);
    }
}
