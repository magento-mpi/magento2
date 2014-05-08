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
use Mtf\Handler\Curl as AbstractCurl;

/**
 * Class Curl
 *
 * @package Magento\Catalog\Test\Handler\CatalogProductGrouped
 */
class Curl extends AbstractCurl implements CatalogProductGroupedInterface
{
    public function persist(FixtureInterface $fixture = null)
    {
        //
    }
}
