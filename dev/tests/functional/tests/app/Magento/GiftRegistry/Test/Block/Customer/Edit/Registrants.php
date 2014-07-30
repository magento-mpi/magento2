<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Customer\Edit;

use Mtf\Client\Driver\Selenium\Element;
use Mtf\Client\Element\Locator;

/**
 * Class Registrant
 * Recipients Information
 */
class Registrants extends Element
{
    /**
     * Recipient firstname selector
     *
     * @var string
     */
    protected $recipientFirstname = '[name="registrant[%s][firstname]"]';

    /**
     * Recipient lastname selector
     *
     * @var string
     */
    protected $recipientLastname = '[name="registrant[%s][lastname]"]';

    /**
     * Recipient email selector
     *
     * @var string
     */
    protected $recipientEmail = '[name="registrant[%s][email]"]';

    /**
     * Recipient role selector
     *
     * @var string
     */
    protected $recipientRole = '[name="registrant[%s][role]"]';

    /**
     * Add registrant button selector
     *
     * @var string
     */
    protected $addRegistrant = '#add-registrant-button';

    /**
     * Registrant block selector
     *
     * @var string
     */
    protected $registrant = '[id="registrant:%d"]';

    /**
     * Recipient roles
     *
     * @var array
     */
    protected $roles = [
        'mom' => 'Mother',
        'dad' => 'Father',
        'groom' => 'Groom',
        'bride' => 'Bride',
        'partner' => 'Partner'
    ];

    /**
     * Set recipients information
     *
     * @param array $value
     * @return void
     */
    public function setValue($value)
    {
        foreach ($value as $key => $recipient) {
            if ($key !== 0) {
                $this->find($this->addRegistrant)->click();
            }
            $this->find(sprintf($this->recipientFirstname, $key))->setValue($recipient['firstname']);
            $this->find(sprintf($this->recipientLastname, $key))->setValue($recipient['lastname']);
            $this->find(sprintf($this->recipientEmail, $key))->setValue($recipient['email']);
            if (isset($recipient['role'])) {
                $this->find(sprintf($this->recipientRole, $key), Locator::SELECTOR_CSS, 'select')
                    ->setValue($recipient['role']);
            }
        }
    }

    /**
     * Get recipients information
     *
     * @return array
     */
    public function getValue()
    {
        $recipients = [];
        $key = 0;
        while ($this->find(sprintf($this->registrant, $key))->isVisible()) {
            $recipients[$key]['firstname'] = $this->find(sprintf($this->recipientFirstname, $key))->getValue();
            $recipients[$key]['lastname'] = $this->find(sprintf($this->recipientLastname, $key))->getValue();
            $recipients[$key]['email'] = $this->find(sprintf($this->recipientEmail, $key))->getValue();
            if ($this->find(sprintf($this->recipientRole, $key))->isVisible()) {
                $recipients[$key]['role'] = $this->roles[$this->find(sprintf($this->recipientRole, $key))->getValue()];
            }
            ++$key;
        }

        return $recipients;
    }
}
