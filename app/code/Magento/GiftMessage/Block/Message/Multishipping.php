<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Block\Message;

class Multishipping extends Inline
{
    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $this->setDontDisplayContainer(false)
            ->setType('multishipping_address')
            ->setEntity($this->getAddressEntity());
        return $this->isMessagesAvailable() ? parent::toHtml() : '';
    }
}
