<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Usps\Helper;

use Magento\App\Helper\AbstractHelper;

/**
 * Usps data helper
 */
class Data extends AbstractHelper
{
    /**
     * Available shipping methods
     *
     * @var array
     */
    protected $availableShippingMethods = array(
        'usps_0_FCLE',
        'usps_1',
        'usps_2',
        'usps_3',
        'usps_4',
        'usps_6',
        'usps_INT_1',
        'usps_INT_2',
        'usps_INT_4',
        'usps_INT_7',
        'usps_INT_8',
        'usps_INT_9',
        'usps_INT_10',
        'usps_INT_11',
        'usps_INT_12',
        'usps_INT_14',
        'usps_INT_16',
        'usps_INT_20',
        'usps_INT_26'
    );

    /**
     * Define if we need girth parameter in the package window
     *
     * @param string $shippingMethod
     * @return bool
     */
    public function displayGirthValue($shippingMethod)
    {
        return in_array($shippingMethod, $this->availableShippingMethods);
    }
}
