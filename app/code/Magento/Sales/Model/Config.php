<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales total nodes config model
 */
namespace Magento\Sales\Model;

class Config implements \Magento\Sales\Model\ConfigInterface
{
    /**
     * Modules configuration model
     *
     * @var \Magento\Sales\Model\Config\Data
     */
    protected $_dataContainer;

    /**
     * @param \Magento\Sales\Model\Config\Data $dataContainer
     */
    public function __construct(\Magento\Sales\Model\Config\Data $dataContainer)
    {
        $this->_dataContainer = $dataContainer;
    }

    /**
     * Retrieve renderer for area from config
     *
     * @param string $section
     * @param string $group
     * @param string $code
     * @param string $area
     * @return array
     */
    public function getTotalsRenderer($section, $group, $code, $area)
    {
        $path = implode('/', array($section, $group, $code, 'renderers', $area));
        return $this->_dataContainer->get($path);
    }

    /**
     * Retrieve totals for group
     * e.g. quote, nominal_totals, etc
     *
     * @param string $section
     * @param string $group
     * @return array
     */
    public function getGroupTotals($section, $group)
    {
        $path = implode('/', array($section, $group));
        return $this->_dataContainer->get($path);
    }

    /**
     * Get available product types
     *
     * @return array
     */
    public function getAvailableProductTypes()
    {
        return $this->_dataContainer->get('order/available_product_types');
    }
}
