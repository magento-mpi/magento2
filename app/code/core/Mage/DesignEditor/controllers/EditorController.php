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

    /**
     * Enforce admin session with the active design editor mode
     *
     * @return Mage_DesignEditor_EditorController
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $this->_session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        if (!$this->_session->isDesignEditorActive()) {
            Mage::getSingleton('Mage_Core_Model_Session')->addError(
                $this->__('Design editor is not initialized by administrator.')
            );
            $this->norouteAction();
            $this->setFlag('', self::FLAG_NO_DISPATCH, true);
        }

        return $this;
    }

    /**
     * Display an arbitrary page by specified page type
     */
    public function pageAction()
    {
        try {
            $pageType = $this->getRequest()->getParam('page_type');

            // page type format
            if (!$pageType && !preg_match('/^[a-z\d]+(_[a-z\d]+){2}$/i', $pageType)) {
                Mage::throwException($this->__('Invalid page type specified.'));
            }

            // whether such page type exists
            $area = Mage::getDesign()->getArea();
            $package = Mage::getDesign()->getPackageName();
            $theme = Mage::getDesign()->getTheme();
            $xml = $this->getLayout()->getUpdate()->getFileLayoutUpdatesXml($area, $package, $theme);
            if (!$xml->xpath("/layouts/{$pageType}")) {
                Mage::throwException($this->__("Specified page type doesn't exist: '{$pageType}'."));
            }

            $this->_fullActionName = $pageType;
            $this->loadLayout(null, false, true);
            Mage::getModel('Mage_DesignEditor_Model_Layout')->sanitizeLayout($this->getLayout()->getNode());
            $this->getLayout()->generateBlocks();
            $this->renderLayout();
            return;
        } catch (Mage_Core_Exception $e) {
            $this->getResponse()->setBody($e->getMessage());
        } catch (Exception $e) {
            $this->getResponse()->setBody($this->__('Unable to load specified page. See error log for details.'));
            if (Mage::getIsDeveloperMode()) {
                echo $e;
            } else {
                Mage::logException($e);
            }
        }
        $this->getResponse()->setHeader('Content-Type', 'text/plain; charset=UTF-8')->setHttpResponseCode(500);
    }

    /**
     * Hack the "full action name" in order to render emulated layout
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

    /**
     * Sets new skin for viewed store and returns customer back to the previous address
     */
    public function skinAction()
    {
        $skin = $this->getRequest()->get('skin');
        $backUrl = $this->_getRefererUrl();

        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        try {
            $session->setSkin($skin);
        } catch (Exception $e) {
            $session->addException($e, $e->getMessage());
        }
        $this->getResponse()->setRedirect($backUrl);
    }
}
