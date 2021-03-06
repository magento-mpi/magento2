<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Invitation\Test\Block;

use Mtf\Client\Element;
use Mtf\Fixture\FixtureInterface;

/**
 * Class Form
 * Send Invitations form
 */
class Form extends \Mtf\Block\Form
{
    /**
     * Send Invitations button
     *
     * @var string
     */
    protected $sendInvitationsButton = '.action.submit';

    /**
     * Add email button
     *
     * @var string
     */
    protected $addEmail = '.add';

    /**
     * Click 'Send Invitations' button
     *
     * @return void
     */
    public function sendInvitations()
    {
        $this->_rootElement->find($this->sendInvitationsButton)->click();
    }

    /**
     * Fill form
     *
     * @param FixtureInterface $invitation
     * @param Element|null $element
     * @return $this
     */
    public function fill(FixtureInterface $invitation, Element $element = null)
    {
        $data = $invitation->getData();
        $mapping = $this->dataMapping($data);
        $emailCount = count($data['email']);
        while ($emailCount > 1) {
            $this->_rootElement->find($this->addEmail)->click();
            $emailCount--;
        }
        $this->_fill($mapping, $element);

        return $this;
    }
}
