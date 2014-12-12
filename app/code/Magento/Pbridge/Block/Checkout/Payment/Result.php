<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Pbridge\Block\Checkout\Payment;

/**
 * Pbridge result payment block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Result extends \Magento\Framework\View\Element\Template
{
    /**
     * Pbridge data
     *
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData = null;

    /**
     * Json encoder interface
     *
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        array $data = []
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->_pbridgeData = $pbridgeData;
        parent::__construct($context, $data);
    }

    /**
     * Return JSON array of Payment Bridge incoming data
     *
     * @return string
     */
    public function getJsonHiddenPbridgeParams()
    {
        return $this->_jsonEncoder->encode($this->_pbridgeData->getPbridgeParams());
    }
}
