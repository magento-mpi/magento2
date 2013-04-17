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
            /** @var $layout Mage_DesignEditor_Model_Layout */
            $layout = $this->getLayout();
            $layoutClassName = Mage_DesignEditor_Model_State::LAYOUT_DESIGN_CLASS_NAME;
            if (!($layout instanceof $layoutClassName)) {
                throw new InvalidArgumentException($this->__('Incorrect Design Editor layout.'));
            }

            /** @var $helper Mage_DesignEditor_Helper_Data */
            $helper = $this->_objectManager->get('Mage_DesignEditor_Helper_Data');

            $handle = $this->getRequest()->getParam('handle');

            if (!$handle || !preg_match('/^[a-z][_a-z\d]*$/i', $handle)
                || !$this->getLayout()->getUpdate()->pageHandleExists($handle)
            ) {
                $handle = $helper->getDefaultHandle();

                /** @var $backendSession Mage_Backend_Model_Session */
                $backendSession = $this->_objectManager->get('Mage_Backend_Model_Session');
                $backendSession->unsetData('vde_current_handle');
                $backendSession->unsetData('vde_current_url');
            }

            // required layout handle
            $this->_fullActionName = $handle;

            // set sanitize and wrapping flags
            $layout->setSanitizing(true);

            // Only allow drag and drop when inline translation is disabled
            $layout->setWrapping(!$helper->isAllowed());

            $this->loadLayout(array(
                'default',
                parent::getFullActionName() // current action layout handle
            ));
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
