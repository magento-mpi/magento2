<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block;

/**
 * RMA Return Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Link extends \Magento\View\Element\Html\Link\Current
{
    /**
     * @var \Magento\Rma\Helper\Data
     */
    protected $_rmaHelper = null;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\App\DefaultPathInterface $defaultPath
     * @param \Magento\Rma\Helper\Data $rmaHelper
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\App\DefaultPathInterface $defaultPath,
        \Magento\Rma\Helper\Data $rmaHelper,
        array $data = array()
    ) {
        parent::__construct($context, $defaultPath, $data);
        $this->_rmaHelper = $rmaHelper;
    }

    /**
     * {@inheritdoc}
     *
     * @return void
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
