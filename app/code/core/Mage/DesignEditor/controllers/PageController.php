<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design Editor controller which performs page actions
 */
class Mage_DesignEditor_PageController extends Mage_Core_Controller_Front_Action
{
    /**
     * Variable to store full action name
     *
     * @var string
     */
    protected $_fullActionName = '';

    /**
     * Check backend session
     */
    public function preDispatch()
    {
        parent::preDispatch();

        // user must be logged in admin area
        /** @var $backendSession Mage_Backend_Model_Auth_Session */
        $backendSession = $this->_objectManager->get('Mage_Backend_Model_Auth_Session');
        if (!$backendSession->isLoggedIn()) {
            if ($this->getRequest()->getActionName() != 'noroute') {
                $this->_forward('noroute');
            }
        }
    }

    /**
     * Render specified page type layout handle
     *
     * @throws InvalidArgumentException
     */
    public function typeAction()
    {
        try {
            $handle = $this->getRequest()->getParam('handle');

            // check page type format
            if (!$handle || !preg_match('/^[a-z][a-z\d]*(_[a-z][a-z\d]*)*$/i', $handle)) {
                throw new InvalidArgumentException($this->__('Invalid page handle specified.'));
            }

            /** @var $layout Mage_DesignEditor_Model_Layout */
            $layout = $this->getLayout();
            $layoutClassName = Mage_DesignEditor_Controller_Varien_Router_Standard::LAYOUT_CLASS_NAME;
            if (!($layout instanceof $layoutClassName)) {
                throw new InvalidArgumentException($this->__('Incorrect Design Editor layout.'));
            }

            // whether such page type exists
            if (!$this->getLayout()->getUpdate()->pageHandleExists($handle)) {
                throw new InvalidArgumentException(
                    $this->__('Specified page type or page fragment type doesn\'t exist: "%s".', $handle)
                );
            }

            // required layout handle
            $this->_fullActionName = $handle;

            // current action layout handle
            $layout->getUpdate()->addHandle(parent::getFullActionName());

            // set sanitize and wrapping flags
            $layout->setSanitizing(true);
            $layout->setWrapping(true);

            $this->loadLayout();
            $this->renderLayout();
        } catch (Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
            $this->getResponse()->setHeader('Content-Type', 'text/plain; charset=UTF-8')->setHttpResponseCode(503);
        }
    }

    /**
     * Replace full action name to render emulated layout
     *
     * @param string $delimiter
     * @return string
     */
    public function getFullActionName($delimiter = '_')
    {
        if ($this->_fullActionName) {
            return $this->_fullActionName;
        }
        return parent::getFullActionName($delimiter);
    }
}

