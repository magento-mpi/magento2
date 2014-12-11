<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Rma\Test\Page;

use Mtf\Client\Element\Locator;
use Mtf\Factory\Factory;
use Mtf\Page\Page;

/**
 * Class ReturnItem
 * Return Item page
 *
 */
class RmaGuestCreate extends Page
{
    /**
     * URL for return page
     */
    const MCA = 'rma/guest/create';

    /**
     * Form wrapper selector
     *
     * @var string
     */
    protected $formWrapperSelector = '//form[@id="rma_create_form"]';

    /**
     * Custom constructor
     */
    protected function _init()
    {
        $this->_url = $this->_url = $_ENV['app_frontend_url'] . self::MCA;
    }

    /**
     * Get return item block form
     *
     * @return \Magento\Rma\Test\Block\Returns\Create
     */
    public function getReturnItemForm()
    {
        return Factory::getBlockFactory()->getMagentoRmaReturnsCreate(
            $this->_browser->find($this->formWrapperSelector, Locator::SELECTOR_XPATH)
        );
    }
}
