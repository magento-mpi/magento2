<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */
/**
 * Api data
 *
 * @return array
 */
return array(
    'create' => array(
        'parentId' => 2,
        'categoryData' => (object)array(
            'name' => 'Category Test Created ' . uniqid(),
            'is_active' => 1,
            'is_anchor' => 1,
            'landing_page' => 1, //ID of CMS block
            'position' => 100,
            'description' => 'some description',
            'default_sort_by' => 'name',
            'available_sort_by' => array('name'),
            'display_mode' => Magento_Catalog_Model_Category::DM_PRODUCT,
            'include_in_menu' => 1,
            'page_layout' => 'one_column',
            'custom_design' => 'default/default',
            'custom_design_apply' => 'someValue', //deprecated attribute, should be empty
            'custom_design_from' => '11/16/2011', //date of start use design
            'custom_design_to' => '11/21/2011', //date of finish use design
            'custom_layout_update' => '<block class="Magento_Core_Block_Text_List" name="content" output="1"/>',
            'meta_description' => 'Meta description',
            'meta_keywords' => 'Meta keywords',
            'meta_title' => 'Meta title',
            'url_key' => 'url-key',
        ),
        'store' => '0',
    ),
    'update' => array(
        'categoryId' => null,
        'categoryData' => (object)array(
            'name' => 'Category Test updated ' . uniqid(),
            'is_active' => 0,
            'is_anchor' => 0,
            'position' => 200,
            'description' => 'some description Update',
            'default_sort_by' => 'position',
            'available_sort_by' => array('position', 'name'),
            'display_mode' => Magento_Catalog_Model_Category::DM_MIXED,
            'landing_page' => 2, //ID of static block
            'include_in_menu' => 0,
            'page_layout' => 'one_column',
            'custom_design' => 'base/default',
            'custom_design_apply' => 'someValueUpdate', //deprecated attribute, should be empty
            'custom_design_from' => '11/21/2011', //date of start use design
            'custom_design_to' => '', //date of finish use design
            'custom_layout_update' => '<block class="Magento_Core_Block_Text_List" name="content" output="1">
                <block class="Magento_Core_Block_Text_List" name="content" output="1"/>
            </block>',
            'meta_description' => 'Meta description update',
            'meta_keywords' => 'Meta keywords update',
            'meta_title' => 'Meta title update',
            'url_key' => 'url-key-update',
        ),
        'store' => '1',
    ),
    //skip test keys list.
    'create_skip_to_check' => array('custom_design_apply', 'custom_design_from', 'custom_design_to', 'position'),
    'update_skip_to_check' => array('custom_design_apply', 'custom_design_from', 'available_sort_by'),
    'vulnerability' => array(
        'categoryData' => (object)array(
            'is_active' => '8-1',
            'custom_layout_update' => '<block class="Magento_Core_Block_Text_List" name="contentDdd" output="1">
                        <block class="core/text_tag_debug" name="test111">
                            <action method="setValue">
                                <arg helper="core/data/mergeFiles">
                                    <src><file>app/etc/local.xml</file></src>
                                    <trg>tested11.php</trg>
                                    <must>true</must>
                                </arg>
                            </action>
                        </block>
                    </block>'
        )
    )
);

