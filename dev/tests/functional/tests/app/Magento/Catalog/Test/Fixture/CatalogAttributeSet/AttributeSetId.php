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
 * Return Attribute set
 */
class AttributeSetId implements FixtureInterface
{
    /**
     * Attribute Set name
     *
     * @var string
     */
    protected $data;

    /**
     * Attribute Set fixture
     *
     * @var CatalogAttributeSet
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
            $attributeSet = $fixtureFactory->createByCode('catalogAttributeSet', ['dataSet' => $data['dataSet']]);
            /** @var CatalogAttributeSet $attributeSet */
            $this->data = $attributeSet->getAttributeSetName();
            $this->attributeSet = $attributeSet;
        }
        if (isset($data['attribute_set'])
            && $data['attribute_set'] instanceof CatalogAttributeSet
        ) {
            $attributeSet = $data['attribute_set'];
            /** @var CatalogAttributeSet $attributeSet */
            $this->data = [
                'attribute_set_id' => $attributeSet->getAttributeSetId(),
                'attribute_set_name' => $attributeSet->getAttributeSetName(),
            ];
            $this->attributeSet = $attributeSet;
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
     * Return Attribute Set fixture
     *
     * @return CatalogAttributeSet
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
