<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * RMA Return Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block;

class Link extends \Magento\Page\Block\Link\Current
{
    /**
     * @var \Magento\Rma\Helper\Data
     *
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaHelper = null;

    /**
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Rma\Helper\Data $rmaHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Helper\Data $coreData,
        \Magento\Core\Block\Template\Context $context,
        \Magento\Rma\Helper\Data $rmaHelper,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_rmaHelper = $rmaHelper;
    }

    /**
     * @inheritdoc
     */
    protected function _toHtml()
    {
        if ($this->_rmaHelper->isEnabled()) {
            return parent::_toHtml();
        } else {
            return '';
        }
    }
}
