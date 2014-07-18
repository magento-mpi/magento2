<?php
/**
 * {license_notice}
 *
 * @spi
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order\Create;

use Mtf\Block\Block;
use Mtf\Factory\Factory;
use Mtf\Client\Element\Locator;
use Magento\Sales\Test\Fixture\Order;

/**
 * Class Store
 * Adminhtml sales order create select store block
 *
 */
class Store extends Block
{
    /**
     * Backend abstract block
     *
     * @var string
     */
    protected $templateBlock = './ancestor::body';

    /**
     * Get backend abstract block
     *
     * @return \Magento\Backend\Test\Block\Template
     */
    protected function getTemplateBlock()
    {
        return Factory::getBlockFactory()->getMagentoBackendTemplate(
            $this->_rootElement->find($this->templateBlock, Locator::SELECTOR_XPATH)
        );
    }

    /**
     * Select store view for order based on Order fixture
     *
     * @param Order|null $fixture
     */
    public function selectStoreView(Order $fixture = null)
    {
        if (!$this->isVisible()) {
            return;
        }
        $storeName = $fixture->getStoreViewName() == null ? 'Default Store View' : $fixture->getStoreViewName();
        $selector = '//label[text()="' . $storeName . '"]/preceding-sibling::*';
        $this->_rootElement->find($selector, Locator::SELECTOR_XPATH, 'checkbox')->setValue('Yes');
        $this->getTemplateBlock()->waitLoader();
    }
}
