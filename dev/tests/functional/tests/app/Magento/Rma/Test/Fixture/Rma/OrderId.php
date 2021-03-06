<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Fixture\Rma;

use Magento\Sales\Test\Fixture\OrderInjectable;
use Mtf\Fixture\FixtureFactory;
use Mtf\Fixture\FixtureInterface;

/**
 * Source rma order id.
 */
class OrderId implements FixtureInterface
{
    /**
     * Data set configuration settings.
     *
     * @var array
     */
    protected $params;

    /**
     * Prepared dataSet data.
     *
     * @var integer
     */
    protected $data = null;

    /**
     * Order source.
     *
     * @var OrderInjectable
     */
    protected $order = null;

    /**
     * @constructor
     * @param FixtureFactory $fixtureFactory
     * @param array $params
     * @param array $data [optional]
     */
    public function __construct(FixtureFactory $fixtureFactory, array $params, array $data = [])
    {
        $this->params = $params;

        $preset = isset($data['preset']) ? $data['preset'] : '';
        $data =  isset($data['data']) ? $data['data'] : [];
        if ($data) {
            $this->order = $fixtureFactory->createByCode(
                'orderInjectable',
                [
                    'dataSet' => $preset,
                    'data' => $data
                ]
            );
            if (!$this->order->hasData('id')) {
                $this->order->persist();
            }
            $this->data = $this->order->getData('id');
        }
    }

    /**
     * Persist custom selections products.
     *
     * @return void
     */
    public function persist()
    {
        //
    }

    /**
     * Return prepared data set.
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
     * Return data set configuration settings.
     *
     * @return string
     */
    public function getDataConfig()
    {
        return $this->params;
    }

    /**
     * Return order source.
     *
     * @return OrderInjectable|null
     */
    public function getOrder()
    {
        return $this->order;
    }
}
