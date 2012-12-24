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
 * Observer for design editor module
 */
class Mage_DesignEditor_Model_Observer
{
    /**
     * @var Mage_Backend_Model_Session
     */
    protected $_backendSession;

    /**
     * @var Mage_Core_Model_Design_Package
     */
    protected $_design;

    /**
     * @param Mage_Backend_Model_Session $backendSession
     * @param Mage_Core_Model_Design_Package $design
     */
    public function __construct(
        Mage_Backend_Model_Session $backendSession,
        Mage_Core_Model_Design_Package $design
    ) {
        $this->_backendSession = $backendSession;
        $this->_design         = $design;
    }

    /**
     * Set specified design theme
     */
    public function setTheme()
    {
        $themeId = $this->_backendSession->getData('theme_id');
        if ($themeId !== null) {
            $this->_design->setDesignTheme($themeId);
        }
    }
}
