<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * After installation system has two categories: root one with ID:1 and Default category with ID:2
 */
/** @var $category \Magento\Catalog\Model\Category */
$categories = array(
    [
        'id' => 3,
        'name' => 'Category 1',
        'parent_id' => 2,
        'path' => '1/2/3',
        'level' => 2,
        'available_sort_by' => 'name',
        'default_sort_by' => 'name',
        'is_active' => true,
        'position' => 1
    ],
    [
        'id' => 4,
        'name' => 'Category 1.1',
        'parent_id' => 3,
        'path' => '1/2/3/4',
        'level' => 3,
        'available_sort_by' => 'name',
        'default_sort_by' => 'name',
        'is_active' => true,
        'position' => 1
    ],
    [
        'id' => 5,
        'name' => 'Category 1.1.1',
        'parent_id' => 4,
        'path' => '1/2/3/4/5',
        'level' => 4,
        'available_sort_by' => 'name',
        'default_sort_by' => 'name',
        'is_active' => true,
        'position' => 1
    ]
);
foreach ($categories as $data) {
    $model = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create('Magento\Catalog\Model\Category');
    $model->setId($data['id'])
        ->setName($data['name'])
        ->setParentId($data['parent_id'])
        ->setPath($data['path'])
        ->setLevel($data['level'])
        ->setAvailableSortBy($data['available_sort_by'])
        ->setDefaultSortBy($data['default_sort_by'])
        ->setIsActive($data['is_active'])
        ->setPosition($data['position'])
        ->save();
}
