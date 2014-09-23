<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Store\Test\TestStep;

use Magento\Sales\Test\Page\Adminhtml\OrderCreateIndex;
use Mtf\TestStep\TestStepInterface;
use Magento\Store\Test\Fixture\Store;

/**
 * Class SelectStoreStep
 * Creating sales rule
 */
class SelectStoreStep implements TestStepInterface
{
    /**
     * Store fixture
     *
     * @var Store
     */
    protected $store;

    /**
     * Order Create Index page
     *
     * @var OrderCreateIndex
     */
    protected $orderCreateIndex;

    /**
     * Preparing step properties
     *
     * @constructor
     * @param Store $store
     * @param OrderCreateIndex $orderCreateIndex
     */
    public function __construct(Store $store, OrderCreateIndex $orderCreateIndex)
    {
        $this->store = $store;
        $this->orderCreateIndex = $orderCreateIndex;
    }

    /**
     * Create sales rule
     *
     * @return array
     */
    public function run()
    {
        if ($this->orderCreateIndex->getStoreBlock()->isVisible()) {
            $this->orderCreateIndex->getStoreBlock()->selectStoreView($this->store);
        }
    }
}
