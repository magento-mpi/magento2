<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GiftWrapping\Model\Wrapping;

use Magento\GiftWrapping\Model\Wrapping;

class Validator
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
     * @param Wrapping $wrapping
     * @return array
     */
    public function validate(Wrapping $wrapping)
    {
        $warnings = [];
        foreach ($this->requiredFields as $code => $label) {
            if (!$wrapping->hasData($code)) {
                $warnings[$code] = 'Field is required: ' . $label;
            }
        }
        return $warnings;
    }
}
