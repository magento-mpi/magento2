<?php
/**
 * {license_notice}
 *
 * @category    Mtf
 * @package     Mtf
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\TestCase\Category;

use Mtf\Factory\Factory;
use Mtf\TestCase\Functional;

/**
 * Class CreateTest
 * Create new subcategory test
 *
 * @package Magento\Catalog\Test\TestCase\Category
 */
class CreateTest extends Functional
{
    /**
     * Category create
     */
    public function testCreateCategory()
    {
        //Data
        $category = Factory::getFixtureFactory()->getMagentoCatalogCategory()->switchData('subcategory');
        //Pages & Blocks
        $catalogCategoryPage = Factory::getPageFactory()->getAdminCatalogCategory();
        $treeBlock = $catalogCategoryPage->getTreeBlock();
        $formBlock = $catalogCategoryPage->getFormBlock();
        $messageBlock = $catalogCategoryPage->getMessageBlock();
        $loader = $catalogCategoryPage->getTemplateBlock();
        //Steps
        Factory::getApp()->magentoBackendLoginUser();
        $catalogCategoryPage->open();
        $treeBlock->selectDefaultCategory();
        $loader->waitLoader();
        $treeBlock->addSubcategory();
        $loader->waitLoader();
        $formBlock->fill($category);
        $formBlock->save($category);
        //Verifying
        $messageBlock->waitForSuccessMessage($category);
    }
}
