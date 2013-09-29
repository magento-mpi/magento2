<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Payment CC Types Source Model
 */
namespace Magento\Payment\Model\Source;

class Cctype implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Allowed CC types
     *
     * @var array
     */
    protected $_allowedTypes = array();

    /**
     * Payment config model
     *
     * @var \Magento\Payment\Model\Config
     */
    protected $_paymentConfig;

    /**
     * Config
     *
     * @param \Magento\Payment\Model\Config $paymentConfig
     */
    public function __construct(\Magento\Payment\Model\Config $paymentConfig)
    {
        $this->_paymentConfig = $paymentConfig;
    }

    /**
     * Return allowed cc types for current method
     *
     * @return array
     */
    public function getAllowedTypes()
    {
        return $this->_allowedTypes;
    }

    /**
     * Setter for allowed types
     *
     * @param $values
     * @return \Magento\Payment\Model\Source\Cctype
     */
    public function setAllowedTypes(array $values)
    {
        $this->_allowedTypes = $values;
        return $this;
    }

    public function toOptionArray()
    {
        /**
         * making filter by allowed cards
         */
        $allowed = $this->getAllowedTypes();
        $options = array();

        foreach ($this->_paymentConfig->getCcTypes() as $code => $name) {
            if (in_array($code, $allowed) || !count($allowed)) {
                $options[] = array(
                   'value' => $code,
                   'label' => $name
                );
            }
        }

        return $options;
    }
}
