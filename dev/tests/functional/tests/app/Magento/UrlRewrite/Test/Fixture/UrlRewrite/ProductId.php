<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UrlRewrite\Test\Fixture\UrlRewrite;

use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Class ProductId
 * Prepare product
 */
class ProductId implements FixtureInterface
{
    /**
     * Resource data
     *
     * @var string
     */
    protected $data;

    /**
     * Return product
     *
     * @var FixtureInterface
     */
    protected $product;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        $explodeValue = explode('::', $data['dataSet']);
        if (!empty($explodeValue) && count($explodeValue) > 1) {
            /** @var FixtureInterface $fixture */
            $this->product = $fixtureFactory->createByCode($explodeValue[0],['dataSet' => $explodeValue[1]]);
            $this->product->persist();
            $this->data =  $this->product->getId();
        } else {
            $this->data = strval($data['dataSet']);
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
     * Return prepared data
     *
     * @param string|null $key
     * @return string
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
     * Return product
     *
     * @return FixtureInterface
     */
    public function getProduct()
    {
        return $this->product;
    }
}
