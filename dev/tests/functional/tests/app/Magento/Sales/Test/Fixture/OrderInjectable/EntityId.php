<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Fixture\OrderInjectable;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class EntityId
 * EntityId data
 */
class EntityId implements FixtureInterface
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
     * Current preset
     *
     * @var string
     */
    protected $currentPreset;

    /**
     * Constructor
     *
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $data
     * @param array $params [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $data, array $params = [])
    {
        $this->params = $params;

        if (isset($data['value'])) {
            $this->data = $data['value'];
            return;
        }

        if (!isset($data['products'])) {
            return;
        }
        if (is_string($data['products'])) {
            $products = explode(',', $data['products']);
            foreach ($products as $product) {
                list($fixture, $dataSet) = explode('::', $product);
                $product = $fixtureFactory->createByCode($fixture, ['dataSet' => $dataSet]);
                $product->persist();
                $this->data['products'][] = $product;
            }
        } elseif (is_array($data['products'])) {
            $products = $data['products'];
            foreach ($products as $product) {
                $this->data['products'][] = $product;
            }
        }
    }

    /**
     * Persist order products
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
     * @param string $key [optional]
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
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }
}
