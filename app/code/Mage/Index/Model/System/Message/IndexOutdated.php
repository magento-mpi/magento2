<?php
/**
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Index_Model_System_Message_IndexOutdated implements Mage_Backend_Model_System_MessageInterface
{
    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed()
    {
        return true;
    }

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText()
    {
        return '<div class="message message-system"><div class="message-inner"><div class="message-content">' .
            '<strong>' . $this->helper('Mage_Index_Helper_Data')->__('One or more of the Indexes are not up to date:') . '</strong>' .
            'SOME TEXT' .
            $this->helper("Mage_Index_Helper_Data")->__('Click here to go to <a href="%s">Index Management</a> and rebuild required indexes.', $this->getManageUrl()) .
            '</div></div></div>';
    }

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity()
    {
        return self::SEVERITY_CRITICAL;
    }
}
