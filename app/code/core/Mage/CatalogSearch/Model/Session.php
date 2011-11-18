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
    public function __construct()
    {
        $this->init('catalogsearch');
    }
}
