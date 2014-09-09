<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GroupedProduct\Test\Handler\GroupedProductInjectable;

use Mtf\Fixture\FixtureInterface;
use Mtf\Util\Protocol\CurlTransport;
use Magento\Catalog\Test\Handler\CatalogProductSimple\Curl as AbstractCurl;

/**
 * Class Curl
 * Create new grouped product via curl
 */
class Curl extends AbstractCurl implements GroupedProductInjectableInterface
{
    /**
     * Prepare POST data for creating product request
     *
     * @param FixtureInterface $fixture
     * @param string|null $prefix [optional]
     * @return array
     */
    protected function prepareData(FixtureInterface $fixture, $prefix = null)
    {
        $data = parent::prepareData($fixture, null);

        $assignedProducts = [];
        if (!empty($data['associated'])) {
            $assignedProducts = $data['associated']['assigned_products'];
            unset($data['associated']);
        }

        $data = $prefix ? [$prefix => $data] : $data;
        foreach ($assignedProducts as $item) {
            $data['links']['associated'][$item['id']] = $item;
        }

        return $data;
    }
}
