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
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 *
 * @package Magento\Catalog\Test\Handler\CatalogProductVirtual
 */
class Curl extends AbstractCurl implements CatalogProductVirtualInterface
{
    public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
