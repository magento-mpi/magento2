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
 * Controller that allows to display arbitrary page in design editor mode
 */
class Mage_DesignEditor_EditorController extends Mage_Core_Controller_Front_Action
{
    /**
     * @var Mage_DesignEditor_Model_Session
     */
    protected $_session = null;

    /**
     * @var string
     */
    protected $_fullActionName = '';

    public function preDispatch()
    {
        parent::preDispatch();

        $this->_session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        if (!$this->_session->isDesignEditorActive()) {
            Mage::getSingleton('Mage_Core_Model_Session')->addError($this->__('Administrator is not logged in.'));
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }

        return $this;
    }

    public function pageAction()
    {
        try {
            $pageType = $this->getRequest()->getParam('page_type');
            if ($pageType && preg_match('/^[a-z\d]+(_[a-z\d]+){2}$/i', $pageType)) {
                $this->_fullActionName = $pageType;
                // check if this page type exists at all (declared in layout as handle)
            }
            if (!$this->_fullActionName) {
                Mage::throwException($this->__('Empty or invalid page type specified.'));
            }

            $this->loadLayout(null, false, true);
//            echo $this->getLayout()->getNode()->asNiceXml();
//            exit;
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setBody($this->__('Unable to load specified page. See error log for details.'));
            Mage::logException($e);
        }
        $this->getResponse()->setHeader('Content-Type', 'text/plain; charset=UTF-8')->setHttpResponseCode(500);
    }

    public function getFullActionName($delimiter = '_')
    {
        if ($this->_fullActionName) {
            return $this->_fullActionName;
        }
        return parent::getFullActionName($delimiter);
    }
}
