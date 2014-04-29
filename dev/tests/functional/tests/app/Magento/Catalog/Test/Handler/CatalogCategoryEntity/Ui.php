<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogCategoryEntity; 

use Magento\Catalog\Test\Handler\CatalogCategoryEntity\CatalogCategoryEntityInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 *
 * @package Magento\Catalog\Test\Handler\CatalogCategoryEntity
 */
class Ui extends AbstractUi implements CatalogCategoryEntityInterface
{
   public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
