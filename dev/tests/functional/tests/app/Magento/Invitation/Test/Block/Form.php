<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
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
        $this->_fill($mapping, $element);

        return $this;
    }
}
