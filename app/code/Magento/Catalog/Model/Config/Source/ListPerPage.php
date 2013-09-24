<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog products per page on List mode source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Config_Source_ListPerPage implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var array
     */
    protected $_pagerOptions;

    /**
     * @param string $options
     */
    public function __construct($options)
    {
        $this->_pagerOptions = explode(',', $options);
    }

    public function toOptionArray()
    {
        $output = array();
        foreach ($this->_pagerOptions as $option) {
            $output[] = array('value' => $option, 'label' => $option);
        }
        return $output;
    }
}
