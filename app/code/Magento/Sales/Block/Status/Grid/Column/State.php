<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Status\Grid\Column;

class State extends \Magento\Backend\Block\Widget\Grid\Column
{
    /**
     * @var \Magento\Sales\Model\Order\Config
     */
    protected $_config;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param \Magento\Sales\Model\Order\Config $config
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\Order\Config $config,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);

        $this->_config = $config;
    }

    /**
     * Add decorated status to column
     *
     * @return array
     */
    public function getFrameCallback()
    {
        return array($this, 'decorateState');
    }

    /**
     * Decorate status column values
     *
     * @param string $value
     * @param \Magento\Sales\Model\Order\Status $row
     * @param \Magento\Adminhtml\Block\Widget\Grid\Column $column
     * @param bool $isExport
     * @return string
     */
    public function decorateState($value, $row, $column, $isExport)
    {
        if ($value) {
            $cell = $value . '[' . $this->_config->getStateLabel($value) . ']';
        } else {
            $cell = $value;
        }
        return $cell;
    }
}
