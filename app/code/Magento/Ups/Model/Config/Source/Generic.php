<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Ups\Model\Config\Source;

/**
 * Class Generic
 */
class Generic implements \Magento\Option\ArrayInterface
{
    /**
     * @var \Magento\Ups\Helper\Config
     */
    protected $carrierConfig;

    /**
     * Carrier code
     *
     * @var string
     */
    protected $_code = '';

    /**
     * @param \Magento\Ups\Helper\Config $carrierConfig
     */
    public function __construct(\Magento\Ups\Helper\Config $carrierConfig)
    {
        $this->carrierConfig = $carrierConfig;
    }

    /**
     * Returns array to be used in multiselect on back-end
     *
     * @return array
     */
    public function toOptionArray()
    {
        $configData = $this->carrierConfig->getCode($this->_code);
        $arr = array();
        foreach ($configData as $code => $title) {
            $arr[] = array('value' => $code, 'label' => __($title));
        }
        return $arr;
    }
}
