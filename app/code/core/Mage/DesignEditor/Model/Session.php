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
     * Check whether the design editor is active for the current session or not
     *
     * @return bool
     */
    public function isDesignEditorActive()
    {
        return (bool)$this->getData(self::SESSION_DESIGN_EDITOR_ACTIVE) && $this->isLoggedIn();
    }

    /**
     * Activate the design editor for the current session
     */
    public function activateDesignEditor()
    {
        $this->setData(self::SESSION_DESIGN_EDITOR_ACTIVE, 1);
    }

    /**
     * Deactivate the design editor for the current session
     */
    public function deactivateDesignEditor()
    {
        $this->unsetData(self::SESSION_DESIGN_EDITOR_ACTIVE);
    }
}
