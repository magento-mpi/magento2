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
namespace Magento\GoogleShopping\Block\Adminhtml\Types\Renderer;

class Country
    extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Config
     *
     * @var \Magento\GoogleShopping\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\GoogleShopping\Model\Config $config
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\GoogleShopping\Model\Config $config,
        array $data = array()
    ) {
        $this->_config = $config;
        parent::__construct($context, $data);
    }

    /**
     * Renders Google Content Item Id
     *
     * @param   \Magento\Object $row
     * @return  string
     */
    public function render(\Magento\Object $row)
    {
        $iso = $row->getData($this->getColumn()->getIndex());
        return $this->_config->getCountryInfo($iso, 'name');
    }
}
