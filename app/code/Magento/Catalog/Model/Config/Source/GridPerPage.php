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
 * Catalog products per page on Grid mode source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class GridPerPage implements \Magento\Option\ArrayInterface
{
    /**
     * Options
     *
     * @var array
     */
    protected $_options;

    /**
     * Constructor
     *
     * @param string $perPageValues
     */
    public function __construct($perPageValues)
    {
        $this->_options = explode(',', $perPageValues);
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        $result = array();
        foreach ($this->_options as $option) {
            $result[] = array('value' => $option, 'label' => $option);
        }
        return $result;
    }
}
