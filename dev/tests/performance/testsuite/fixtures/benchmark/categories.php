<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/** @var \Magento\TestFramework\Application $this */

/**
 * Create categories
 */
$categoriesNumber = \Magento\TestFramework\Helper\Cli::getOption('categories', 10);
$maxNestingLevel = \Magento\TestFramework\Helper\Cli::getOption('categories_nesting_level', 3);
$anchorStep = 2;

$nestingLevel = 1;
$parentCategoryId = $defaultParentCategoryId = $this->getObjectManager()->get(
    'Magento\Framework\StoreManagerInterface'
)->getStore()->getRootCategoryId();
$nestingPath = "1/{$parentCategoryId}";
$categoryPath = '';
$categoryIndex = 1;

$categories = array();

$category = $this->getObjectManager()->create('Magento\Catalog\Model\Category');
while ($categoryIndex <= $categoriesNumber) {
    $category->setId(
        null
    )->setName(
        "Category {$categoryIndex}"
    )->setParentId(
        $parentCategoryId
    )->setPath(
        $nestingPath
    )->setLevel(
        $nestingLevel
    )->setAvailableSortBy(
        'name'
    )->setDefaultSortBy(
        'name'
    )->setIsActive(
        true
    )->setIsAnchor(
        $categoryIndex++ % $anchorStep == 0
    )->save();

    $categoryPath .= '/' . $category->getName();
    $categories[] = ltrim($categoryPath, '/');

    if ($nestingLevel++ == $maxNestingLevel) {
        $nestingLevel = 1;
        $parentCategoryId = $defaultParentCategoryId;
        $nestingPath = '1';
        $categoryPath = '';
    } else {
        $parentCategoryId = $category->getId();
    }
    $nestingPath .= "/{$parentCategoryId}";
}
