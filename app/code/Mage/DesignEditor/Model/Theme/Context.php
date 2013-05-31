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
 * Design editor theme context
 */
class Mage_DesignEditor_Model_Theme_Context
{
    /**
     * Session key of editable theme
     */
    const CURRENT_THEME_SESSION_KEY = 'vde_theme_id';

    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @var Mage_Core_Model_Theme_Factory
     */
    protected $_themeFactory;

    /**
     * @var Mage_Captcha_Helper_Data
     */
    protected $_helper;

    /**
     * @var Mage_Core_Model_Theme_CopyService
     */
    protected $_copyService;

    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Mage_Core_Model_Theme
     */
    protected $_stagingTheme;

    /**
     * Initialize dependencies
     *
     * @param Mage_Backend_Model_Session $backendSession
     * @param Mage_Core_Model_Theme_Factory $themeFactory
     * @param Mage_Core_Helper_Data $helper
     * @param Mage_Core_Model_Theme_CopyService $copyService
     */
    public function __construct(
        Mage_Backend_Model_Session $backendSession,
        Mage_Core_Model_Theme_Factory $themeFactory,
        Mage_Core_Helper_Data $helper,
        Mage_Core_Model_Theme_CopyService $copyService
    ) {
        $this->_backendSession = $backendSession;
        $this->_themeFactory = $themeFactory;
        $this->_helper = $helper;
        $this->_copyService = $copyService;
    }

    /**
     * Reset checked theme
     *
     * @return Mage_DesignEditor_Model_State
     */
    public function reset()
    {
        $this->_backendSession->unsetData(self::CURRENT_THEME_SESSION_KEY);
        return $this;
    }

    /**
     * Set theme which will be editable in store designer
     *
     * @param int $themeId
     * @return $this
     */
    public function setEditableThemeId($themeId)
    {
        $this->_backendSession->setData(self::CURRENT_THEME_SESSION_KEY, $themeId);
        return $this;
    }

    /**
     * Get theme which selected as editable in launcher
     *
     * @return int|null
     */
    public function getEditableThemeId()
    {
        return $this->_backendSession->getData(self::CURRENT_THEME_SESSION_KEY);
    }

    /**
     * @return Mage_Core_Model_Theme
     * @throws Mage_Core_Exception
     */
    public function getEditableTheme()
    {
        if (!$this->_theme) {
            $themeId = $this->getEditableThemeId();
            $this->_theme = $this->_themeFactory->create();
            if (!($themeId && $this->_theme->load($themeId)->getId())) {
                throw new Mage_Core_Exception($this->_helper->__('We can\'t find theme "%s".', $themeId));
            }
        }
        return $this->_theme;
    }

    /**
     * @return Mage_Core_Model_Theme
     * @throws Mage_Core_Exception
     */
    public function getStagingTheme()
    {
        $editableTheme = $this->getEditableTheme();
        if (!$editableTheme->isVirtual()) {
            throw new Mage_Core_Exception($this->_helper->__('Theme "%s" can\'t be edited', $editableTheme->getId()));
        }
        $stagingTheme = $editableTheme->getDomainModel(Mage_Core_Model_Theme::TYPE_VIRTUAL)->getStagingTheme();
        return $stagingTheme;
    }

    /**
     * Theme which can be rendered on store designer
     *
     * @return Mage_Core_Model_Theme
     */
    public function getVisibleTheme()
    {
        $editableTheme = $this->getEditableTheme();
        return $editableTheme->isVirtual() ? $this->getStagingTheme() : $editableTheme;
    }

    /**
     * Copy all changed data related to launched theme from staging theme
     *
     * @return $this
     */
    public function copyChanges()
    {
        $this->_copyService->copy($this->getStagingTheme(), $this->getEditableTheme());
        return $this;
    }
}
