<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\GoogleShopping\Model\Source;

/**
 * Google Content Target country Source
 *
 * @category   Magento
 * @package    Magento_GoogleShopping
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Country implements \Magento\Option\ArrayInterface
{
    /**
     * Config
     *
     * @var \Magento\GoogleShopping\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\GoogleShopping\Model\Config $config
     */
    public function __construct(\Magento\GoogleShopping\Model\Config $config)
    {
        $this->_config = $config;
    }

    /**
     * Retrieve option array with allowed countries
     *
     * @return array
     */
    public function toOptionArray()
    {
        $_allowed = $this->_config->getAllowedCountries();
        $result = array();
        foreach ($_allowed as $iso => $info) {
            $result[] = array('value' => $iso, 'label' => $info['name']);
        }
        return $result;
    }
}
