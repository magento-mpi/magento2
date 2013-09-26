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
namespace Magento\Catalog\Model\Config\Source;

class ListPerPage implements \Magento\Core\Model\Option\ArrayInterface
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
