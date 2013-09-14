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
    array('LAST_PRODUCT_COOKIE', 'Magento\FullPageCache\Model\Processor'),
    array('NO_CACHE_COOKIE', 'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\Processor\RestrictionInterface::NO_CACHE_COOKIE'
    ),
    array('XML_PATH_DEFAULT_TIMEZONE', 'Magento\CatalogEvent\Model\Event'),
    array('METADATA_CACHE_SUFFIX', 'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\MetadataInterface::METADATA_CACHE_SUFFIX'
    ),
    array('REQUEST_ID_PREFIX', 'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\Request\Identifier::REQUEST_ID_PREFIX'
    ),
    array('DESIGN_EXCEPTION_KEY', 'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\DesignPackage\Info::DESIGN_EXCEPTION_KEY'
    ),
    array('DESIGN_CHANGE_CACHE_SUFFIX', 'Magento\FullPageCache\Model\Processor',
        'Magento\FullPageCache\Model\DesignPackage\Rules::DESIGN_CHANGE_CACHE_SUFFIX'
    ),
);
