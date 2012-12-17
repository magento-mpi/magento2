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
 * Design editor session model
 *
 * @method int getThemeId()
 * @method Mage_DesignEditor_Model_Session setThemeId($themeId)
 */
class Mage_DesignEditor_Model_Session extends Mage_Backend_Model_Auth_Session
{
    /**#@+
     * Session key that indicates whether the design editor/preview is active
     */
    const SESSION_DESIGN_EDITOR_ACTIVE = 'DESIGN_EDITOR_ACTIVE';
    const SESSION_DESIGN_PREVIEW_ACTIVE = 'DESIGN_PREVIEW_ACTIVE';
    /**#@-*/

    /**
     * Cookie name, which indicates whether highlighting of elements is enabled or not
     */
    const COOKIE_HIGHLIGHTING = 'vde_highlighting';

    /**
     * Application Event Dispatcher
     *
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventDispatcher;

    /**
     * System Event Manager
     *
     * @var Magento_ObjectManager
     */
    protected $_objectManager;

    /**
     * Initialize dependencies
     *
     * @param Mage_Core_Model_Event_Manager $eventDispatcher
     * @param Magento_ObjectManager $objectManager
     * @param array $data
     */
    public function __construct(
        Mage_Core_Model_Event_Manager $eventDispatcher,
        Magento_ObjectManager $objectManager,
        $data = array()
    ) {
        parent::__construct($data);
        $this->_eventDispatcher = $eventDispatcher;
        $this->_objectManager = $objectManager;
    }

    /**
     * Check whether the design editor is active for the current session or not
     *
     * @return bool
     */
    public function isDesignEditorActive()
    {
        return $this->getData(self::SESSION_DESIGN_EDITOR_ACTIVE) && $this->isLoggedIn();
    }

    /**
     * Check whether the design preview is active for the current session or not
     *
     * @return bool
     */
    public function isDesignPreviewActive()
    {
        return (bool)$this->getData(self::SESSION_DESIGN_PREVIEW_ACTIVE);
    }

    /**
     * Activate the design editor for the current session
     */
    public function activateDesignEditor()
    {
        if (!$this->isDesignEditorActive()) {
            $this->setData(self::SESSION_DESIGN_EDITOR_ACTIVE, 1);
            $this->_eventDispatcher->dispatch('design_editor_session_activate', array());
        }
    }

    /**
     * Activate the design preview for the current session
     */
    public function activateDesignPreview()
    {
        if (!$this->isDesignPreviewActive()) {
            $this->setData(self::SESSION_DESIGN_PREVIEW_ACTIVE, 1);
            $this->_eventDispatcher->dispatch('design_editor_session_activate', array());
        }
    }

    /**
     * Deactivate the design editor for the current session
     */
    public function deactivateDesignEditor()
    {
        /*
         * isLoggedIn() is intentionally not taken into account to be able to trigger event when admin session expires
         */
        if ($this->getData(self::SESSION_DESIGN_EDITOR_ACTIVE)) {
            $this->unsetData(self::SESSION_DESIGN_EDITOR_ACTIVE);
            $this->_objectManager->get('Mage_Core_Model_Cookie')->delete(self::COOKIE_HIGHLIGHTING);
            $this->_eventDispatcher->dispatch('design_editor_session_deactivate', array());
        }
    }

    /**
     * Deactivate the design preview for the current session
     */
    public function deactivateDesignPreview()
    {
        if ($this->getData(self::SESSION_DESIGN_PREVIEW_ACTIVE)) {
            $this->unsetData(self::SESSION_DESIGN_PREVIEW_ACTIVE);
            $this->_eventDispatcher->dispatch('design_editor_session_deactivate', array());
        }
    }


    /**
     * Check whether highlighting of elements is disabled or not
     *
     * @return bool
     */
    public function isHighlightingDisabled()
    {
        $highlighting = $this->_objectManager->get('Mage_Core_Model_Cookie')->get(self::COOKIE_HIGHLIGHTING);
        return 'off' == $highlighting;
    }

    /**
     * Get url to frontend page with regenerated sid parameter
     *
     * @param mixed $storeId
     * @return string
     */
    public function getPreviewUrl($storeId = true)
    {
        $query = array(Mage_Core_Model_Session_Abstract::SESSION_ID_QUERY_PARAM => urlencode($this->getSessionId()));
        if (!Mage::app()->isSingleStoreMode() && $storeId) {
            $params = array('_store' => $storeId);
            $store = Mage::app()->getStore($storeId);
            $query['___store'] = urlencode($store->getCode());
        }
        $params['_nosid'] = true;
        $params['_query'] = $query;

        /** @var $urlModel Mage_Core_Model_Url */
        $urlModel = $this->_objectManager->create('Mage_Core_Model_Url');
        return $urlModel->getUrl('/', $params);
    }
}
