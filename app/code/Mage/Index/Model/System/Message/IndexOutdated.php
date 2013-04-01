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
        // TODO: Implement isDisplayed() method.
    }

    /**
     * Retrieve message text
     *
     * @return text
     */
    public function getText()
    {
        $title = $this->helper('Mage_Index_Helper_Data')->__('One or more of the Indexes are not up to date:');
        return '<strong>' . $this->helper('Mage_Index_Helper_Data')->__('One or more of the Indexes are not up to date:') ?></strong>
<?php echo implode(', ', $_processes)?>.
<?php echo $this->helper('Mage_Index_Helper_Data')->__('Click here to go to <a href="%s">Index Management</a> and rebuild required indexes.', $this->getManageUrl());?>

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
