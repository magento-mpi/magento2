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
 * Frontend toolbar panel for the design editor controls
 */
class Mage_DesignEditor_Block_Toolbar extends Mage_Core_Block_Template
{
    /**
     * Prevent rendering if the design editor is inactive
     *
     * @return string
     */
    protected function _toHtml()
    {
        /** @var $session Mage_DesignEditor_Model_Session */
        $session = Mage::getSingleton('Mage_DesignEditor_Model_Session');
        if (!$session->isDesignEditorActive()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Returns messages for Visual Design Editor, clears list of session messages
     *
     * @return array
     */
    public function getMessages()
    {
        return Mage::getSingleton('Mage_DesignEditor_Model_Session')
            ->getMessages(true)
            ->getItems();
    }

    /**
     * Get configuration options for Visual Design Editor as JSON
     *
     * @return string
     */
    public function getOptionsJson()
    {
        $options = array(
            'cookie_highlighting_name' => Mage_DesignEditor_Model_Session::COOKIE_HIGHLIGHTING,
        );
        return Mage::helper('Mage_Core_Helper_Data')->jsonEncode($options);
    }
}
