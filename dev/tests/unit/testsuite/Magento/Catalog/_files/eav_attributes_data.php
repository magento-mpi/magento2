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
    //empty attribute case
    array(false, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE, false),//Event Type, result
    //attribute exists, but shouldn't be matched
    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE, false),//Event Type, result
    //Next cases describe situation that one valuable argument exists
    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 1),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 1),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE, false),//Event Type, result

    array(true, true, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 1),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 1),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 1),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 1),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 1)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_DELETE, true),//Event Type, result

    //\Magento\Index\Model\Event::TYPE_SAVE cases
    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, false),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, true),//Event Type, result

    array(true, true, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, false),//Event Type, result

    array(true, true, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 1),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, true),//Event Type, result

    array(true, true, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 1),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 1),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 1),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 1),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 1),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 1)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 0)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, true),//Event Type, result

    array(true, false, //Attribute, isAddFilterable
        //Original attribute data
        array(
            array('backend_type', 'not_static'),
            array('is_filterable', 0),
            array('used_in_product_listing', 0),
            array('is_used_for_promo_rules', 0),
            array('used_for_sort_by', 0)
        ),
        //Attribute data
        array(
            array('backend_type', null, 'not_static'),
            array('is_filterable', null, 0),
            array('used_in_product_listing', null, 0),
            array('is_used_for_promo_rules', null, 0),
            array('used_for_sort_by', null, 1)
        ),
        \Magento\Index\Model\Event::TYPE_SAVE, true),//Event Type, result
);