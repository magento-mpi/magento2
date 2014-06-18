<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogCategory;

use Mtf\Fixture\FixtureInterface;
use Mtf\Fixture\FixtureFactory;
use Magento\Catalog\Test\Fixture\CatalogCategory;

/**
 * Class ParentId
 * Prepare parent category
 */
class ParentId implements FixtureInterface
{
    /**
     * Return category
     *
     * @var CatalogCategory
     */
    protected $parentCategory = null;

    /**
     * Fixture params
     *
     * @var array
     */
    protected $params;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array|int $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, $data = [])
    {
        if ($data['dataSet']) {
            $this->parentCategory = $fixtureFactory->createByCode('catalogCategory', ['entity' => $data['dataSet']]);
            if (!$this->parentCategory->getId()) {
                $this->parentCategory->persist();
            }
            $this->data = $this->parentCategory->getId();
        } else {
            $this->data = $data;
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

    /**
     * Return entity
     *
     * @return CatalogCategory
     */
    public function getParentCategory()
    {
        return $this->parentCategory;
    }
}
