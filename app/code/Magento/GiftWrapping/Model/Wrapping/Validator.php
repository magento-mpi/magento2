<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Wrapping;

use Magento\GiftWrapping\Model\Wrapping;

class Validator extends \Magento\Framework\Validator\AbstractValidator
{
    /**
     * @var array
     */
    protected $requiredFields = [
        'design' => 'Gift Wrapping Design',
        'status' => 'Status',
        'base_price' => 'Price'
    ];

    /**
     * Returns true if and only if $value meets the validation requirements
     *
     * If $value fails validation, then this method returns false, and
     * getMessages() will return an array of messages that explain why the
     * validation failed.
     *
     * @param \Magento\GiftWrapping\Model\Wrapping $wrapping
     * @return bool
     */
    public function isValid($wrapping)
    {
        $warnings = [];
        foreach ($this->requiredFields as $code => $label) {
            if (!$wrapping->hasData($code)) {
                $warnings[$code] = 'Field is required: ' . $label;
            }
        }

        $this->_addMessages($warnings);
        return empty($warnings);
    }
}
