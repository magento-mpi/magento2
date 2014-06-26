<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Paypal\Model\System\Config\Source;

class BmlPosition
{
    /**
     * Bml positions source getter for Home Page
     *
     * @return array
     */
    public function getBmlPositionsHP()
    {
        return [
            '0' => __('Header (center)'),
            '1' => __('Sidebar (right)')
        ];
    }

    /**
     * Bml positions source getter for Catalog Category Page
     *
     * @return array
     */
    public function getBmlPositionsCCP()
    {
        return [
            '0' => __('Header (center)'),
            '1' => __('Sidebar (right)')
        ];
    }

    /**
     * Bml positions source getter for Catalog Product Page
     *
     * @return array
     */
    public function getBmlPositionsCPP()
    {
        return [
            '0' => __('Header (center)'),
            '1' => __('Near Bill Me Later checkout button')
        ];
    }

    /**
     * Bml positions source getter for Checkout Cart Page
     *
     * @return array
     */
    public function getBmlPositionsCheckout()
    {
        return [
            '0' => __('Header (center)'),
            '1' => __('Near Bill Me Later checkout button')
        ];
    }
}
