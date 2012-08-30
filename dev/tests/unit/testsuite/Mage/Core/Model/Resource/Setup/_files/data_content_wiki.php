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
            'field',
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK,
            Mage_Core_Model_Resource_Setup_Migration::FIELD_CONTENT_TYPE_WIKI,
            ''
        )
    ),
    '$tableData' => array(
        array('field' => '<p>{{widget type="productalert/product_view"}}</p>'),
        array('field' => '<p>Some HTML code</p>'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => '<p>{{widget type="Mage_ProductAlert_Block_Product_View"}}</p>',
                'from'  => '<p>{{widget type="productalert/product_view"}}</p>'
            ),
        ),
        'aliases_map' => array(
            Mage_Core_Model_Resource_Setup_Migration::ENTITY_TYPE_BLOCK => array(
                'productalert/product_view' => 'Mage_ProductAlert_Block_Product_View',
            )
        )
    ),
);
