<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Handler\CatalogProductVirtual;

use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as AbstractCurl;

/**
 * Class Curl
 * Create new virtual product via curl
 */
class Curl extends AbstractCurl implements CatalogProductVirtualInterface
{
    /**
     * Post request for creating virtual product
     *
     * @param FixtureInterface $fixture
     * @return array
     */
    public function persist(FixtureInterface $fixture = null)
    {
        return parent::persist($fixture);
    }
}
