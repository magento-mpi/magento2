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
 * Class SkeletonSet
 *
 *  Data keys:
 *  - dataSet
 */
class SkeletonSet implements FixtureInterface
{
    /**
     * Attribute Set name
     *
     * @var string
     */
    protected $data;

    /**
     * New Attribute Set
     *
     * @var array
     */
    protected $attributeSet;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataSet']) && $data['dataSet'] !== '-') {
            $parentSet = $fixtureFactory->createByCode('catalogAttributeSet', ['dataSet' => $data['dataSet']]);
            if (!$parentSet->hasData('attribute_set_id')) {
                $parentSet->persist();
            }
            /** @var CatalogAttributeSet $parentSet */
            $this->data = $parentSet->getAttributeSetName();
            $this->attributeSet = $parentSet;
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
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getData($key = null)
    {
        return $this->data;
    }

    /**
     * Get Attribute Set
     *
     * @return array
     */
    public function getAttributeSet()
    {
        return $this->attributeSet;
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
