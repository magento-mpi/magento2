<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
/** @var \Magento\TestFramework\Application $this */
$categoriesNumber = \Magento\TestFramework\Helper\Cli::getOption('categories', 18);
$maxNestingLevel = \Magento\TestFramework\Helper\Cli::getOption('categories_nesting_level', 3);
$this->resetObjectManager();

/** @var \Magento\Core\Model\StoreManager $storeManager */
$storeManager = $this->getObjectManager()->create('\Magento\Core\Model\StoreManager');
/** @var $category \Magento\Catalog\Model\Category */
$category = $this->getObjectManager()->create('Magento\Catalog\Model\Category');

$groups = array();
$storeGroups = $storeManager->getGroups();
$i = 0;
foreach ($storeGroups as $storeGroup) {
    $parentCategoryId[$i] = $defaultParentCategoryId[$i] = $storeGroup->getRootCategoryId();
    $nestingLevel[$i] = 1;
    $nestingPath[$i] = "1/$parentCategoryId[$i]";
    $categoryPath[$i] = '';
    $i++;
}
$group_number = 0;
$anchorStep = 2;
$categoryIndex = 1;

while ($categoryIndex <= $categoriesNumber) {
    $category->setId(null)
        ->setName("Category $categoryIndex")
        ->setParentId($parentCategoryId[$group_number])
        ->setPath($nestingPath[$group_number])
        ->setLevel($nestingLevel[$group_number])
        ->setAvailableSortBy('name')
        ->setDefaultSortBy('name')
        ->setIsActive(true)
        //->setIsAnchor($categoryIndex++ % $anchorStep == 0)
        ->save();
    $categoryIndex++;
    $categoryPath[$group_number] .=  '/' . $category->getName();

    if ($nestingLevel[$group_number]++ == $maxNestingLevel) {
        $nestingLevel[$group_number] = 1;
        $parentCategoryId[$group_number] = $defaultParentCategoryId[$group_number];
        $nestingPath[$group_number] = '1';
        $categoryPath[$group_number] = '';
    } else {
        $parentCategoryId[$group_number] = $category->getId();
    }
    $nestingPath[$group_number] .= "/$parentCategoryId[$group_number]";

    $group_number++;
    if ($group_number==count($defaultParentCategoryId)) {
        $group_number = 0;
    }
}


