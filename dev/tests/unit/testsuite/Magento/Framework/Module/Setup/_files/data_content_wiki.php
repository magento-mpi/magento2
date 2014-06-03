<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    '$replaceRules' => array(
        array(
            'table',
            'field',
            \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK,
            \Magento\Framework\Module\Setup\Migration::FIELD_CONTENT_TYPE_WIKI
        )
    ),
    '$tableData' => array(
        array('field' => '<p>{{widget type="productalert/product_view"}}</p>'),
        array('field' => '<p>{{widget type="catalogSearch/result"}}</p>'),
        array('field' => '<p>Some HTML code</p>')
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to' => '<p>{{widget type="Magento\ProductAlert\Block\Product\View"}}</p>',
                'from' => array('`field` = ?' => '<p>{{widget type="productalert/product_view"}}</p>')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to' => '<p>{{widget type="Magento\CatalogSearch\Block\Result"}}</p>',
                'from' => array('`field` = ?' => '<p>{{widget type="catalogSearch/result"}}</p>')
            )
        ),
        'aliases_map' => array(
            \Magento\Framework\Module\Setup\Migration::ENTITY_TYPE_BLOCK => array(
                'productalert/product_view' => 'Magento\ProductAlert\Block\Product\View',
                'catalogSearch/result' => 'Magento\CatalogSearch\Block\Result'
            )
        )
    )
);
