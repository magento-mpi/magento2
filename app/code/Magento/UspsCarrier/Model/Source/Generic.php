<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\UspsCarrier\Model\Source;

use Magento\Core\Model\Option\ArrayInterface;
use Magento\UspsCarrier\Model\Usps;

/**
 * Generic source
 */
class Generic implements ArrayInterface
{
    /**
     * @var \Magento\UspsCarrier\Model\Usps
     */
    protected $shippingUsps;

    /**
     * Carrier code
     *
     * @var string
     */
    protected $code = '';

    /**
     * @param \Magento\UspsCarrier\Model\Usps $shippingUsps
     */
    public function __construct(Usps $shippingUsps)
    {
        $this->shippingUsps = $shippingUsps;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $codes = $this->shippingUsps->getCode($this->code);
        if ($codes) {
            foreach ($codes as $code => $title) {
                $options[] = ['value' => $code, 'label' => __($title)];
            }
        }
        return $options;
    }
}
