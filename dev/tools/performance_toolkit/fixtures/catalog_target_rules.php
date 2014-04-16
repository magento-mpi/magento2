<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\ToolkitFramework\Application $this */
$catalogTargetRules = \Magento\ToolkitFramework\Config::getInstance()->getValue('catalog_target_rules', 3);
$this->resetObjectManager();

/** @var \Magento\Store\Model\StoreManager $storeManager */
$storeManager = $this->getObjectManager()->create('\Magento\Store\Model\StoreManager');
/** @var $category \Magento\Catalog\Model\Category */
$category = $this->getObjectManager()->get('Magento\Catalog\Model\Category');
/** @var $model  \Magento\TargetRule\Model\Rule*/
$model = $this->getObjectManager()->get('Magento\TargetRule\Model\Rule');
//Get all websites
$categories_array = array();
$websites = $storeManager->getWebsites();
foreach ($websites as $website) {
    //Get all groups
    $website_groups = $website->getGroups();
    foreach ($website_groups as $website_group) {
        $website_group_root_category = $website_group->getRootCategoryId();
        $category->load($website_group_root_category);
        $categoryResource = $category->getResource();
        //Get all categories
        $results_categories = $categoryResource->getAllChildren($category);
        foreach ($results_categories as $results_category) {
            $category->load($results_category);
            $structure = explode('/', $category->getPath());
            if (count($structure) > 2) {
                $categories_array[] = array($category->getId(), $website->getId());
            }
        }
    }
}
asort($categories_array);
$categories_array = array_values($categories_array);
$idField = $model->getIdFieldName();


for ($i = 0; $i < $catalogTargetRules; $i++) {

    //Necessary to create the correct data in magento_targetrule_product table
    $this->resetObjectManager();
    $model = $this->getObjectManager()->get('Magento\TargetRule\Model\Rule');
    //------------

    $ruleName = sprintf('Catalog Target Rule %1$d', $i);
    $data = array(
        $idField                => null,
        'name'                  => $ruleName,
        'sort_order'            => 0,
        'is_active'             => 1,
        'apply_to'              => 1,
        'from_date'             => '',
        'to_date'               => '',
        'positions_limit'       => 10,
        'use_customer_segment'  => 0,
        'customer_segment_ids'  => '',
        'rule'                  => array (
            'conditions' =>
                array (
                    1 =>
                        array (
                            'type' => 'Magento\\TargetRule\\Model\\Rule\\Condition\\Combine',
                            'aggregator' => 'all',
                            'value' => '1',
                            'new_child' => '',
                        ),
                    '1--1' =>
                        array (
                            'type' => 'Magento\\TargetRule\\Model\\Rule\\Condition\\Product\\Attributes',
                            'attribute' => 'category_ids',
                            'operator' => '==',
                            'value' => $categories_array[$i % count($categories_array)][0],
                        ),
                ),
            'actions' =>
                array (
                    1 =>
                        array (
                            'type' => 'Magento\\TargetRule\\Model\\Actions\\Condition\\Combine',
                            'aggregator' => 'all',
                            'value' => '1',
                            'new_child' => '',
                        ),
                    '1--1' =>
                        array (
                            'type' => 'Magento\\TargetRule\\Model\\Actions\\Condition\\Product\\Attributes',
                            'attribute' => 'category_ids',
                            'operator' => '==',
                            'value_type' => 'same_as',
                            'value' => '',
                        ),
                ),
        ),
    );
    if (isset($data['rule']['conditions'])) {
        $data['conditions'] = $data['rule']['conditions'];
    }
    if (isset($data['rule']['actions'])) {
        $data['actions'] = $data['rule']['actions'];
    }
    unset($data['rule']);

    $model->loadPost($data);
    $model->save();
}
