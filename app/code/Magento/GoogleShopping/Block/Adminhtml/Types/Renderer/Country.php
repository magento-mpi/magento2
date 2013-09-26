<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleShopping
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Adminhtml Google Content Item Type Country Renderer
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_GoogleShopping_Block_Adminhtml_Types_Renderer_Country
    extends Magento_Backend_Block_Widget_Grid_Column_Renderer_Abstract
{
    /**
     * Config
     *
     * @var Magento_GoogleShopping_Model_Config
     */
    protected $_config;

    /**
     * @param Magento_GoogleShopping_Model_Config $config
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_GoogleShopping_Model_Config $config,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        $this->_config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Renders Google Content Item Id
     *
     * @param   Magento_Object $row
     * @return  string
     */
    public function render(Magento_Object $row)
    {
        $iso = $row->getData($this->getColumn()->getIndex());
        return $this->_config->getCountryInfo($iso, 'name');
    }
}
