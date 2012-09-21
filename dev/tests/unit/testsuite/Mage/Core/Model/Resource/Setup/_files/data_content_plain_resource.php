<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Core
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    '$replaceRules' => array(
        array(
            'table',
            'collection',
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_RESOURCE,
            Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_PLAIN,
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
                'to'    => 'Mage_Customer_Model_Resource_Attribute_Collection',
                'from'  => array('`collection` = ?' => 'customer/attribute_collection')
            ),
        ),
        'where' => array(
            'flag = 1'
        ),
        'aliases_map' => array(
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_RESOURCE => array(
                'customer/attribute_collection' => 'Mage_Customer_Model_Resource_Attribute_Collection'
            ),
        )
    ),
);
