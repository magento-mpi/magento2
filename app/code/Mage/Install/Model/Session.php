<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Install session model
 */
class Mage_Install_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Init session
     */
    public function __construct()
    {
        $this->init('install');
    }
}
