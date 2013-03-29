<?php
/**
 * Backend system message
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
interface Mage_Backend_Model_System_MessageInterface
{
    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed();

    /**
     * Retrieve message text
     *
     * @return text
     */
    public function getText();

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity();
}
