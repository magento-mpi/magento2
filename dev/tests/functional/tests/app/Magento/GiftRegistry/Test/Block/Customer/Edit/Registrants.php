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
     * Recipient fields selectors
     *
     * @var array
     */
    protected $recipient = [
        'firstname' => '[name$="[firstname]"]',
        'lastname' => '[name$="[lastname]"]',
        'email' => '[name$="[email]"]',
        'role' => '[name$="[role]"]',
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
            $registrant = $this->find(sprintf($this->registrant, $key));
            if ($key !== 0) {
                $this->find($this->addRegistrant)->click();
            }
            foreach ($recipient as $field => $value) {
                if ($field === 'role') {
                    $registrant->find($this->recipient[$field], Locator::SELECTOR_CSS, 'select')->setValue($value);
                } else {
                    $registrant->find($this->recipient[$field])->setValue($value);
                }
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
        $registrant = $this->find(sprintf($this->registrant, $key));
        while ($registrant->isVisible()) {
            $recipients[$key]['firstname'] = $registrant->find($this->recipient['firstname'])->getValue();
            $recipients[$key]['lastname'] = $registrant->find($this->recipient['lastname'])->getValue();
            $recipients[$key]['email'] = $registrant->find($this->recipient['email'])->getValue();
            if ($registrant->find($this->recipient['role'])->isVisible()) {
                $recipients[$key]['role'] = $registrant->find(
                    $this->recipient['role'],
                    Locator::SELECTOR_CSS,
                    'select'
                )->getValue();
            }
            $registrant = $this->find(sprintf($this->registrant, ++$key));
        }

        return $recipients;
    }
}
