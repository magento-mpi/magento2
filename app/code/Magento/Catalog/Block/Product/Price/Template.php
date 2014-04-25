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
 * Catalog Product Price Template Block
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Catalog\Block\Product\Price;

class Template extends \Magento\Framework\View\Element\AbstractBlock
{
    /**
     * Product Price block types cache
     *
     * @var array
     */
    protected $_priceBlockTypes = array();

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param array $priceBlockTypes
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        array $priceBlockTypes = array(),
        array $data = array()
    ) {
        $this->_priceBlockTypes = $priceBlockTypes;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve array of Price Block Types
     *
     * Key is price block type name and value is array of
     * template and block strings
     *
     * @return array
     */
    public function getPriceBlockTypes()
    {
        return $this->_priceBlockTypes;
    }

    /**
     * Adding customized price template for product type
     *
     * @param string $type
     * @param string $block
     * @param string $template
     * @return \Magento\Catalog\Block\Product\Price\Template
     */
    public function addPriceBlockType($type, $block = '', $template = '')
    {
        if ($type) {
            $this->_priceBlockTypes[$type] = array('block' => $block, 'template' => $template);
        }

        return $this;
    }
}
