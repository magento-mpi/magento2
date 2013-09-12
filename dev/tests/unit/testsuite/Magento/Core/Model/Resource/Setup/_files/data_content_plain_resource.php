<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    '$replaceRules' => array(
        array(
            'table',
            'collection',
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_RESOURCE,
            Magento_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
            array(),
            'flag = 1'
        )
    ),
    '$tableData' => array(
        array('collection' => 'customer/attribute_collection'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'collection',
                'to'    => 'Magento_Customer_Model_Resource_Attribute_Collection',
                'from'  => array('`collection` = ?' => 'customer/attribute_collection')
            ),
        ),
        'where' => array(
            'flag = 1'
        ),
        'aliases_map' => array(
            Magento_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_RESOURCE => array(
                'customer/attribute_collection' => 'Magento_Customer_Model_Resource_Attribute_Collection'
            ),
        )
    ),
);
