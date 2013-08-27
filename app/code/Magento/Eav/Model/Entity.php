<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * EAV entity model
 *
 * @category   Magento
 * @package    Magento_Eav
 */
class Magento_Eav_Model_Entity extends Magento_Eav_Model_Entity_Abstract
{
    const DEFAULT_ENTITY_MODEL      = 'Magento_Eav_Model_Entity';
    const DEFAULT_ATTRIBUTE_MODEL   = 'Magento_Eav_Model_Entity_Attribute';
    const DEFAULT_BACKEND_MODEL     = 'Magento_Eav_Model_Entity_Attribute_Backend_Default';
    const DEFAULT_FRONTEND_MODEL    = 'Magento_Eav_Model_Entity_Attribute_Frontend_Default';
    const DEFAULT_SOURCE_MODEL      = 'Magento_Eav_Model_Entity_Attribute_Source_Config';

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
