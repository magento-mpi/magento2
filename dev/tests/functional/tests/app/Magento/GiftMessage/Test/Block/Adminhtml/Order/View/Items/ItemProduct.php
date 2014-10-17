<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Block\Adminhtml\Order\View\Items;

use Magento\GiftMessage\Test\Fixture\GiftMessage;
use Mtf\Block\Form;
use Mtf\Client\Element;
use Mtf\Client\Element\Locator;

/**
 * Class ItemProduct
 * Item product block on OrderView page.
 */
class ItemProduct extends Form
{
    /**
     * Selector for GiftOptions link.
     *
     * @var string
     */
    protected $giftOptionsLink = '[id^="gift_options_link"]';

    /**
     * Selector for order item GiftMessage form.
     *
     * @var string
     */
    protected $giftMessageForm = './ancestor::body//*[@role="dialog" and contains(@style,"block")]';

    /**
     * Get GiftMessage form data.
     *
     * @param GiftMessage $giftMessage
     * @return array
     */
    public function getGiftMessageFormData(GiftMessage $giftMessage)
    {
        $giftOptionsLink = $this->_rootElement->find($this->giftOptionsLink);
        if ($giftOptionsLink->isVisible()) {
            $giftOptionsLink->click();
        }
        /** @var \Magento\GiftMessage\Test\Block\Adminhtml\Order\View\Form $giftMessageForm */
        $giftMessageForm = $this->blockFactory->create(
            'Magento\GiftMessage\Test\Block\Adminhtml\Order\View\Form',
            ['element' => $this->_rootElement->find($this->giftMessageForm, Locator::SELECTOR_XPATH)]
        );
        $data = $giftMessageForm->getData($giftMessage);
        $giftMessageForm->closeDialog();
        return $data;
    }
}
