<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Config\Source;

/**
 * Catalog products per page on List mode source
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class ListPerPage implements \Magento\Framework\Option\ArrayInterface
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
