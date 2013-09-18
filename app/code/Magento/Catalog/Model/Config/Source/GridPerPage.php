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
 * Catalog products per page on Grid mode source
 *
 * @category   Magento
 * @package    Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Model\Config\Source;

class GridPerPage implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * Constructor
     *
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_coreConfig = $coreConfig;
    }

    public function toOptionArray()
    {
        $result = array();
        $perPageValues = $this->_coreConfig->getNode('frontend/catalog/per_page_values/grid');
        $perPageValues = explode(',', $perPageValues);
        foreach ($perPageValues as $option) {
            $result[] = array('value' => $option, 'label' => $option);
        }
        return $result;
    }
}
