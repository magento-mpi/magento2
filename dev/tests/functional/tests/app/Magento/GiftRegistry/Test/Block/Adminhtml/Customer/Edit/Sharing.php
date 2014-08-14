<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftRegistry\Test\Block\Adminhtml\Customer\Edit;

use Mtf\Block\Form;
use Mtf\Client\Element\Locator;

/**
 * Class Sharing
 * Backend sharing gift registry form
 */
class Sharing extends Form
{
    /**
     * Share Gift Registry button selector
     *
     * @var string
     */
    protected $shareGiftRegistry = '[type="submit"]';

    /**
     * Sharing Information fields
     *
     * @var array
     */
    protected $shareInfoFields = [
        'emails' => [
            'selector' => '[name="emails"]',
            'input' => null
        ],
        'store_id' => [
            'selector' => '[name="store_id"]',
            'input' => 'select'
        ],
        'message' => [
            'selector' => '[name="message"]',
            'input' => 'textarea'
        ]
    ];

    /**
     * Click share gift registry
     *
     * @return void
     */
    public function shareGiftRegistry()
    {
        $this->_rootElement->find($this->shareGiftRegistry)->click();
    }

    /**
     * Fill Sharing Information form
     *
     * @param array $sharingInfo
     * @return void
     */
    public function fillForm(array $sharingInfo)
    {
        foreach ($sharingInfo as $field => $value) {
            $this->_rootElement->find(
                $this->shareInfoFields[$field]['selector'],
                Locator::SELECTOR_CSS,
                $this->shareInfoFields[$field]['input']
            )->setValue($value);
        }
    }
}
