<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogProductGrouped;

use Magento\Catalog\Test\Handler\CatalogProductGrouped\CatalogProductGroupedInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 *
 * @package Magento\Catalog\Test\Handler\CatalogProductGrouped
 */
class Ui extends AbstractUi implements CatalogProductGroupedInterface
{
    public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
