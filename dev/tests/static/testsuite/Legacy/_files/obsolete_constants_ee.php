<?php
/**
 * Same as obsolete_constants.php, but specific to Magento EE
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    array('LAST_PRODUCT_COOKIE', 'Enterprise_PageCache_Model_Processor'),
    array('NO_CACHE_COOKIE', 'Enterprise_PageCache_Model_Processor',
        'Enterprise_PageCache_Model_Processor_RestrictionInterface::NO_CACHE_COOKIE'
    ),
    array('XML_PATH_DEFAULT_TIMEZONE', 'Magento_CatalogEvent_Model_Event'),
    array('METADATA_CACHE_SUFFIX', 'Enterprise_PageCache_Model_Processor',
        'Enterprise_PageCache_Model_MetadataInterface::METADATA_CACHE_SUFFIX'
    ),
    array('REQUEST_ID_PREFIX', 'Enterprise_PageCache_Model_Processor',
        'Enterprise_PageCache_Model_Request_Identifier::REQUEST_ID_PREFIX'
    ),
    array('DESIGN_EXCEPTION_KEY', 'Enterprise_PageCache_Model_Processor',
        'Enterprise_PageCache_Model_DesignPackage_Info::DESIGN_EXCEPTION_KEY'
    ),
    array('DESIGN_CHANGE_CACHE_SUFFIX', 'Enterprise_PageCache_Model_Processor',
        'Enterprise_PageCache_Model_DesignPackage_Rules::DESIGN_CHANGE_CACHE_SUFFIX'
    ),
);
