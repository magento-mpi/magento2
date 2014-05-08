<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogProductVirtual;

use Magento\Catalog\Test\Handler\CatalogProductVirtual\CatalogProductVirtualInterface;
use Mtf\Fixture\FixtureInterface;
use Mtf\Handler\Ui as AbstractUi;

/**
 * Class Ui
 *
 * @package Magento\Catalog\Test\Handler\CatalogProductVirtual
 */
class Ui extends AbstractUi implements CatalogProductVirtualInterface
{
    public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
