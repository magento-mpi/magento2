<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Block\Adminhtml\Order;

use Mtf\Client\Element\Locator;

/**
 * Class Form
 * Abstract Form block
 */
abstract class AbstractForm extends \Mtf\Block\Form
{
    /**
     * Send button css selector
     *
     * @var string
     */
    protected $send = '[data-ui-id="order-items-submit-button"]';

    /**
     * Loader css selector
     *
     * @var string
     */
    protected $loader = '#loading_mask_loader';

    /**
     * Fil data
     *
     * @param array $data
     * @return void
     */
    public function fillData(array $data)
    {
        $data = $this->dataMapping($this->prepareData($data));
        $this->_fill($data);
    }

    /**
     * Submit order
     *
     * @return void
     */
    public function submit()
    {
        $browser = $this->browser;
        $selector = $this->loader;
        $browser->waitUntil(
            function () use ($browser, $selector) {
                $element = $browser->find($selector);
                return $element->isVisible() == false ? true : null;
            }
        );
        $this->reinitRootElement();
        $this->_rootElement->find($this->send)->click();
    }

    /**
     * Prepare data
     *
     * @param array $data
     * @return array|null
     */
    protected function prepareData(array $data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $value = $this->prepareData($value);
            }
            if ($value !== '-' && $value !== null) {
                $result[$key] = $value;
            }
        }

        return empty($result) ? null : $result;
    }
}
