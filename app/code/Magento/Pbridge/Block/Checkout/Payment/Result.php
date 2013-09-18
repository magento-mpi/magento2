<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Pbridge result payment block
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Block\Checkout\Payment;

class Result extends \Magento\Core\Block\Template
{
    /**
     * Pbridge data
     *
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData = null;

    /**
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Core\Helper\Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        parent::__construct($coreData, $context, $data);
    }

    /**
     * Return JSON array of Payment Bridge incoming data
     *
     * @return string
     */
    public function getJsonHiddenPbridgeParams()
    {
        return $this->_coreData->jsonEncode(
            $this->_pbridgeData->getPbridgeParams()
        );
    }
}
