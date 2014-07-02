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
    protected $entity = null;

    /**
     * Data set configuration settings
     *
     * @var array
     */
    protected $params;

    /**
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;
        if (!isset($data['entity']) || $data['entity'] === '-') {
            $this->data = array_shift($data);
            return;
        }
        preg_match('`%(.*?)%`', $data['entity'], $dataSet);
        $explodeValue = explode('::', $dataSet[1]);
        if (!empty($explodeValue) && count($explodeValue) > 1) {
            /** @var FixtureInterface $fixture */
            $this->entity = $fixtureFactory->createByCode($explodeValue[0], ['dataSet' => $explodeValue[1]]);
            $this->entity->persist();
            $id = $this->entity->hasData('id') ? $this->entity->getId() : $this->entity->getPageId();
            $this->data = preg_replace('`(%.*?%)`', $id, $data['entity']);
        } else {
            $this->data = strval($data['entity']);
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
     * Return entity
     *
     * @return FixtureInterface
     */
    public function getEntity()
    {
        return $this->entity;
    }
}
