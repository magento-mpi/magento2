<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

return array(
    array(
        false,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE,
        false
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE,
        false
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 1),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 1),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE,
        false
    ),
    array(
        true,
        true,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 1),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 1),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 1),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 1),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 1)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        false
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        true
    ),
    array(
        true,
        true,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        false
    ),
    array(
        true,
        true,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 1),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        true
    ),
    array(
        true,
        true,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 1),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 1),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 1),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 1),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 1),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 1)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        true
    ),
    array(
        true,
        false,
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 1)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE,
        true
    )
);
