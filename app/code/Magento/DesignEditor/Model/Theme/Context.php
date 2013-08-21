<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design editor theme context
 */
class Magento_DesignEditor_Model_Theme_Context
{
    /**
     * @var Magento_Core_Model_ThemeFactory
     */
    protected $_themeFactory;

    /**
     * @var Magento_Core_Model_Theme_CopyService
     */
    protected $_copyService;

    /**
     * @var Magento_Core_Model_Theme
     */
    protected $_theme;

    /**
     * @var Magento_Core_Model_Theme
     */
    protected $_stagingTheme;

    /**
     * Initialize dependencies
     *
     * @param Magento_Core_Model_ThemeFactory $themeFactory
     * @param Magento_Core_Model_Theme_CopyService $copyService
     */
    public function __construct(
        Magento_Core_Model_ThemeFactory $themeFactory,
        Magento_Core_Model_Theme_CopyService $copyService
    ) {
        $this->_themeFactory = $themeFactory;
        $this->_copyService = $copyService;
    }

    /**
     * Reset checked theme
     *
     * @return Magento_DesignEditor_Model_State
     */
    public function reset()
    {
        $this->_theme = null;
        return $this;
    }

    /**
     * Set theme which will be editable in store designer
     *
     * @param int $themeId
     * @return $this
     * @throws Magento_Core_Exception
     */
    public function setEditableThemeById($themeId)
    {
        $this->_theme = $this->_themeFactory->create();
        if (!$this->_theme->load($themeId)->getId()) {
            throw new Magento_Core_Exception(__('We can\'t find theme "%1".', $themeId));
        }
        if ($this->_theme->getType() === Magento_Core_Model_Theme::TYPE_STAGING) {
            throw new Magento_Core_Exception(__('Wrong theme type set as editable'));
        }
        return $this;
    }

    /**
     * Get current editable theme
     *
     * @return Magento_Core_Model_Theme
     * @throws Magento_Core_Exception
     */
    public function getEditableTheme()
    {
        if (null === $this->_theme) {
            throw new Magento_Core_Exception(__('Theme has not been set'));
        }
        return $this->_theme;
    }

    /**
     * Get staging theme
     *
     * @return Magento_Core_Model_Theme
     * @throws Magento_Core_Exception
     */
    public function getStagingTheme()
    {
        if (null === $this->_stagingTheme) {
            $editableTheme = $this->getEditableTheme();
            if (!$editableTheme->isVirtual()) {
                throw new Magento_Core_Exception(
                    __('Theme "%1" is not editable.', $editableTheme->getThemeTitle())
                );
            }
            $this->_stagingTheme = $editableTheme->getDomainModel(Magento_Core_Model_Theme::TYPE_VIRTUAL)
                ->getStagingTheme();
        }
        return $this->_stagingTheme;
    }

    /**
     * Theme which can be rendered on store designer
     *
     * @return Magento_Core_Model_Theme
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
