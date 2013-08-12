<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Core_Model_Session_Generic extends Mage_Core_Model_Session_Abstract
{
    /**
     * @param string $sessionNamespace
     * @param string $sessionName
     */
    public function __construct($sessionNamespace, $sessionName = null)
    {
        $this->init($sessionNamespace, $sessionName);
    }
}
