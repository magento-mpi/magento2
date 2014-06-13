<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogAttributeSet;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogAttributeSet;

/**
 * Class SkeletonSetId
 * Create and return Attribute set
 */
class SkeletonSetId implements FixtureInterface
{
    /**
     * Ids of the created categories
     *
     * @var array
     */
    protected $data;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(
        FixtureFactory $fixtureFactory,
        array $params,
        array $data = []
    ) {
        $this->params = $params;
        if (isset($data['dataSet'])) {
            $parentSet = $fixtureFactory->createByCode('catalogAttributeSet', ['dataSet' => $data['dataSet']]);
            if (!$parentSet->hasData('attribute_set_id')) {
                $parentSet->persist();

                /** @var CatalogAttributeSet $parentSet */
                $this->data = [
                    'attribute_set_id' => $parentSet->getId(),
                ];
            }
        }
    }

    /**
     * Persist attribute options
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set
     *
     * @param string|null $key
     * @return mixed
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Return data set configuration settings
     *
     * @return array
     */
    public function getDataConfig()
    {
        return $this->params;
    }
}
