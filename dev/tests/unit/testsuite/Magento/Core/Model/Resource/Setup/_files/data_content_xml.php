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
            'field',
            \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK,
            \Magento\Core\Model\Resource\Setup\Migration::FIELD_CONTENT_TYPE_XML,
        )
    ),
    '$tableData' => array(
        array('field' => '<reference><block class="catalog/product_new" /></reference>'),
        array('field' => '<reference><block class="catalogSearch/result" /></reference>'),
        array('field' => '<reference></reference>'),
    ),
    '$expected' => array(
        'updates' => array(
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => '<reference><block class="\Magento\Catalog\Block\Product\New" /></reference>',
                'from'  => array('`field` = ?' => '<reference><block class="catalog/product_new" /></reference>')
            ),
            array(
                'table' => 'table',
                'field' => 'field',
                'to'    => '<reference><block class="\Magento\CatalogSearch\Block\Result" /></reference>',
                'from'  => array('`field` = ?' => '<reference><block class="catalogSearch/result" /></reference>')
            ),
        ),
        'aliases_map' => array(
            \Magento\Core\Model\Resource\Setup\Migration::ENTITY_TYPE_BLOCK => array(
                'catalog/product_new'  => '\Magento\Catalog\Block\Product\New',
                'catalogSearch/result' => '\Magento\CatalogSearch\Block\Result',
            )
        )
    ),
);
