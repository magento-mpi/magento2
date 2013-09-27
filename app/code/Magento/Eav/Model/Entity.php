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
     *
     * @param Magento_Core_Model_Resource $resource
     * @param Magento_Eav_Model_Config $eavConfig
     * @param Magento_Eav_Model_Entity_Attribute_Set $attrSetEntity
     * @param Magento_Core_Model_LocaleInterface $locale
     * @param Magento_Eav_Model_Resource_Helper_Mysql4 $resourceHelper
     * @param Magento_Eav_Model_Factory_Helper $helperFactory
     * @param Magento_Core_Model_Resource $coreResource
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Resource $resource,
        Magento_Eav_Model_Config $eavConfig,
        Magento_Eav_Model_Entity_Attribute_Set $attrSetEntity,
        Magento_Core_Model_LocaleInterface $locale,
        Magento_Eav_Model_Resource_Helper_Mysql4 $resourceHelper,
        Magento_Eav_Model_Factory_Helper $helperFactory,
        Magento_Core_Model_Resource $coreResource,
        $data = array()
    ) {
        parent::__construct(
            $resource,
            $eavConfig,
            $attrSetEntity,
            $locale,
            $resourceHelper,
            $helperFactory,
            $data
        );
        $this->setConnection($coreResource->getConnection('eav_read'));
    }
}
