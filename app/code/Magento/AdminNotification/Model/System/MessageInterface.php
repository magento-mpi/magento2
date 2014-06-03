<?php
/**
 * System message
 *
 * {license_notice}
 * 
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\AdminNotification\Model\System;

interface MessageInterface
{
    const SEVERITY_CRITICAL = 1;

    const SEVERITY_MAJOR = 2;

    /**
     * Retrieve unique message identity
     *
     * @return string
     */
    public function getIdentity();

    /**
     * Check whether
     *
     * @return bool
     */
    public function isDisplayed();

    /**
     * Retrieve message text
     *
     * @return string
     */
    public function getText();

    /**
     * Retrieve message severity
     *
     * @return int
     */
    public function getSeverity();
}
