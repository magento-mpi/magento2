<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftMessage\Test\Block\Adminhtml\Order\Create;

use Mtf\Block\Form as ParentForm;
use Mtf\Fixture\FixtureInterface;
use Mtf\Client\Element;

/**
 * Class Form
 * Backend item form for gift message
 */
class Form extends ParentForm
{
    /**
     * Selector for 'OK' button.
     *
     * @var string
     */
    protected $okButton = '#gift_options_ok_button';

    /**
     * Fill backend GiftMessage item form.
     *
     * @param FixtureInterface $fixture
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $fixture, Element $element = null)
    {
        parent::fill($fixture, $element);
        $this->_rootElement->find($this->okButton)->click();
    }
}
