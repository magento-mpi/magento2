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
 */
class Mage_DesignEditor_Model_Session extends Mage_Admin_Model_Session
{
    /**
     * Session key that indicates whether the design editor is active
     */
    const SESSION_DESIGN_EDITOR_ACTIVE = 'DESIGN_EDITOR_ACTIVE';

    /**
     * Cookie name, which indicates whether highlighting of elements is enabled or not
     */
    const COOKIE_HIGHLIGHTING = 'vde_highlighting';

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
     * Activate the design editor for the current session
     */
    public function activateDesignEditor()
    {
        if (!$this->getData(self::SESSION_DESIGN_EDITOR_ACTIVE) && $this->isLoggedIn()) {
            $this->setData(self::SESSION_DESIGN_EDITOR_ACTIVE, 1);
            Mage::dispatchEvent('design_editor_session_activate');
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
            Mage::getSingleton('Mage_Core_Model_Cookie')->delete(self::COOKIE_HIGHLIGHTING);
            Mage::dispatchEvent('design_editor_session_deactivate');
        }
    }

    /**
     * Check whether highlighting of elements is disabled or not
     *
     * @return bool
     */
    public function isHighlightingDisabled()
    {
        $highlighting = Mage::getSingleton('Mage_Core_Model_Cookie')->get(self::COOKIE_HIGHLIGHTING);
        return 'off' == $highlighting;
    }

    /**
     * Sets skin to user session, so that next time everything will be rendered with this skin
     *
     * @param string $skin
     * @return Mage_DesignEditor_Model_Session
     */
    public function setSkin($skin)
    {
        if ($skin && !$this->_isSkinApplicable($skin)) {
            Mage::throwException(Mage::helper('Mage_DesignEditor_Helper_Data')->__("Skin doesn't exist"));
        }
        $this->setData('skin', $skin);
        return $this;
    }

    /**
     * Returns whether a skin is a valid one to set into user session
     *
     * @param string $skin
     * @return bool
     */
    protected function _isSkinApplicable($skin)
    {
        if (!$skin) {
            return false;
        }
        $options = Mage::getModel('Mage_Core_Model_Design_Source_Design')->getOptions();
        foreach ($options as $optGroup) {
            foreach ($optGroup['value'] as $option) {
                if ($option['value'] == $skin) {
                    return true;
                }
            }
        }
        return false;
    }
}
