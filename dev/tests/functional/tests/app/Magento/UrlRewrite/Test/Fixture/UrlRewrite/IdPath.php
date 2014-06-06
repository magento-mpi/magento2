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
 * Class IdPath
 * Prepare ID Path
 */
class IdPath implements FixtureInterface
{
    /**
     * Resource data
     *
     * @var string
     */
    protected $data;

    /**
     * Return category
     *
     * @var FixtureInterface
     */
    protected $category;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        preg_match('`%(.*?)%`', $data['dataSet'], $dataSet);
        $explodeValue = explode('::', $dataSet[1]);
        if (!empty($explodeValue) && count($explodeValue) > 1) {
            /** @var FixtureInterface $fixture */
            $this->category = $fixtureFactory->createByCode($explodeValue[0],['dataSet' => $explodeValue[1]]);
            $this->category->persist();
            $this->data =  preg_replace('`(%.*?%)`', $this->category->getId(), $data['dataSet']);
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
     * Return category
     *
     * @return FixtureInterface
     */
    public function getCategory()
    {
        return $this->category;
    }
}
