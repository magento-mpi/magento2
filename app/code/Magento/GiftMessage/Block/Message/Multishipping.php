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
     * Init parameters for block
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setDontDisplayContainer(false)
            ->setType('multishipping_address');
    }

    /**
     * {@inheritdoc}
     */
    public function toHtml()
    {
        $this->setEntity($this->getAddressEntity());
        return $this->isMessagesAvailable() ? parent::toHtml() : '';
    }
}
