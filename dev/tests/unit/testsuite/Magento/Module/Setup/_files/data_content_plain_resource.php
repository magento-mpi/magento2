<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Module
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    '$replaceRules' => array(
        array(
            'table',
            'collection',
            \Magento\Module\Setup\Migration::ENTITY_TYPE_RESOURCE,
            \Magento\Module\Setup\Migration::FIELD_CONTENT_TYPE_PLAIN,
            array(),
            'flag = 1'
        )
    ),
    '$tableData' => array(array('collection' => 'customer/attribute_collection')),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'collection',
                'to' => 'Magento\Customer\Model\Resource\Attribute\Collection',
                'from' => array('`collection` = ?' => 'customer/attribute_collection')
            )
        ),
        'where' => array('flag = 1'),
        'aliases_map' => array(
            \Magento\Module\Setup\Migration::ENTITY_TYPE_RESOURCE => array(
                'customer/attribute_collection' => 'Magento\Customer\Model\Resource\Attribute\Collection'
            )
        )
    )
);
