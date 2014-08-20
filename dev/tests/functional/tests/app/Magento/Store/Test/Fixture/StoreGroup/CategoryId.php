<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\Fixture\StoreGroup;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;
use Magento\Catalog\Test\Fixture\CatalogCategory;

/**
 * Class CategoryId
 * Prepare CategoryId for Store Group
 */
class CategoryId implements FixtureInterface
{
    /**
     * Prepared dataSet data
     *
     * @var array
     */
    protected $data;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * Array with category fixture
     *
     * @var array
     */
    protected $category;

    /**
     * Constructor
     *
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (isset($data['dataSet'])) {
            $category = $fixtureFactory->createByCode('catalogCategory', ['dataSet' => $data['dataSet']]);
            /** @var CatalogCategory $category */
            if (!$category->getId()) {
                $category->persist();
            }
            $this->category = $category;
            $this->data = $category->getName();
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
     * @param string|null $key [optional]
     * @return mixed
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
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
     * Return category fixture
     *
     * @return array
     */
    public function getCategory()
    {
        return $this->category;
    }
}
