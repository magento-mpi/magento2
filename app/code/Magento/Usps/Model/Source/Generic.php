<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Usps\Model\Source;

use Magento\Shipping\Model\Carrier\Source\GenericInterface;
use Magento\Usps\Model\Carrier;

/**
 * Generic source
 */
class Generic implements GenericInterface
{
    /**
     * @var \Magento\Usps\Model\Carrier
     */
    protected $shippingUsps;

    /**
     * Carrier code
     *
     * @var string
     */
    protected $code = '';

    /**
     * @param \Magento\Usps\Model\Carrier $shippingUsps
     */
    public function __construct(Carrier $shippingUsps)
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
        $options = array();
        $codes = $this->shippingUsps->getCode($this->code);
        if ($codes) {
            foreach ($codes as $code => $title) {
                $options[] = array('value' => $code, 'label' => __($title));
            }
        }
        return $options;
    }
}
