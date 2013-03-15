<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Eav_Model_Entity_Collection extends Mage_Eav_Model_Entity_Collection_Abstract
{
    /**
     * Initialize resource
     */
    public function __construct()
    {
        $resources = Mage::getSingleton('Mage_Core_Model_Resource');
        parent::__construct($resources->getConnection('eav_setup'));
    }
}
