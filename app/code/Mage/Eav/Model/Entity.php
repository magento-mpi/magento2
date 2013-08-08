<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV entity model
 *
 * @category   Mage
 * @package    Mage_Eav
 */
class Mage_Eav_Model_Entity extends Mage_Eav_Model_Entity_Abstract
{
    const DEFAULT_ENTITY_MODEL      = 'Mage_Eav_Model_Entity';
    const DEFAULT_ATTRIBUTE_MODEL   = 'Mage_Eav_Model_Entity_Attribute';
    const DEFAULT_BACKEND_MODEL     = 'Mage_Eav_Model_Entity_Attribute_Backend_Default';
    const DEFAULT_FRONTEND_MODEL    = 'Mage_Eav_Model_Entity_Attribute_Frontend_Default';
    const DEFAULT_SOURCE_MODEL      = 'Mage_Eav_Model_Entity_Attribute_Source_Config';

    const DEFAULT_ENTITY_TABLE      = 'eav_entity';
    const DEFAULT_ENTITY_ID_FIELD   = 'entity_id';

    /**
     * Resource initialization
     */
    public function __construct()
    {
        $resource = Mage::getSingleton('Magento_Core_Model_Resource');
        $this->setConnection($resource->getConnection('eav_read'));
    }

}
