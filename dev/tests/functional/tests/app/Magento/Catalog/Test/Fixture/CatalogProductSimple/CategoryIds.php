<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Fixture\CatalogProductSimple;

use Magento\Catalog\Test\Fixture\CatalogCategoryEntity;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class CategoryIds
 * Create and return Category
 */
class CategoryIds implements FixtureInterface
{
    /**
     * Names and Ids of the created categories
     *
     * @var array
     */
    protected $data;

    /**
     * New categories
     *
     * @var array
     */
    protected $category;

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

        if (!empty($data['category']) && empty($data['presets'])
            && $data['category'] instanceof CatalogCategoryEntity
        ) {
            /** @var CatalogCategoryEntity $category */
            $category = $data['category'];
            $this->data = [
                [
                    'id' => $category->getId(),
                    'name' => $category->getName(),
                ],
            ];
            $this->category[] = $category;
        } elseif (isset($data['presets']) && $data['presets'] !== '-') {
            $presets = explode(',', $data['presets']);
            foreach ($presets as $preset) {
                $category = $fixtureFactory->createByCode('catalogCategoryEntity', ['dataSet' => $preset]);
                $category->persist();

                /** @var CatalogCategoryEntity $category */
                $this->data = [
                    [
                        'id' => $category->getId(),
                        'name' => $category->getName(),
                    ],
                ];
                $this->category[] = $category;
            }
        }
    }

    /**
     * Persist custom selections products
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
     * @param $key [optional]
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
     * Return category array
     *
     * @return array
     */
    public function getCategory()
    {
        return $this->category;
    }
}
