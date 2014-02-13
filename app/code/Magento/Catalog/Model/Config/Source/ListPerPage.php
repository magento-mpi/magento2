<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source;

/**
 * Catalog products per page on List mode source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ListPerPage implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * Pager Options
     *
     * @var array
     */
    protected $_pagerOptions;

    /**
     * Constructor
     *
     * @param string $options
     */
    public function __construct($options)
    {
        $this->_pagerOptions = explode(',', $options);
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $output = array();
        foreach ($this->_pagerOptions as $option) {
            $output[] = array('value' => $option, 'label' => $option);
        }
        return $output;
    }
}
