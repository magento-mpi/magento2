<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_CatalogSearch_Model_Session extends Mage_Core_Model_Session_Abstract
{
    /**
     * Class constructor. Initialize session namespace
     *
     * @param string $sessionName
     */
    public function __construct($sessionName = null)
    {
        $this->init('catalogsearch', $sessionName);
    }
}
