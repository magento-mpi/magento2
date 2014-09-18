<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Block;

use Mtf\Block\Block;

/**
 * Class Check
 * Check block on customer account page
 */
class Check extends Block
{
    /**
     * Filter for get data
     *
     * @var array
     */
    protected $filter = [
        'code' => 'Gift Card: (.*)',
        'balance' => '\nCurrent Balance: \$(.*)',
        'date_expires' => '\nExpires: (\d+\/\d+\/\d+)'
    ];

    /**
     * Get gift card account data
     *
     * @param array $filter
     * @return array
     */
    public function getGiftCardAccountData(array $filter)
    {
        $pattern = '';
        $count = 0;
        $result = [];
        foreach ($filter as $key => $value) {
            if (isset($this->filter[$key])) {
                $pattern .= $this->filter[$key];
                $count++;
            }
        }
        preg_match('/' . $pattern . '/', $this->_rootElement->getText(), $matches);
        if ($count == count($matches) - 1) {
            $index = 1;
            foreach ($filter as $key => $value) {
                $result[$key] = $matches[$index++];
            }
        }
        return $result;
    }
}
