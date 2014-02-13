<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Pbridge\Block\Adminhtml\Sales\Order\Create;

/**
 * Pbridge result payment block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Result extends \Magento\Backend\Block\Template
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
     * @var \Magento\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Json\EncoderInterface $jsonEncoder,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        array $data = array()
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
        return $this->_jsonEncoder->encode(
            $this->_pbridgeData->getPbridgeParams()
        );
    }
}
