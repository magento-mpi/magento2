<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Order;

/**
 * Sales order link
 */
class Link extends \Magento\Page\Block\Link\Current
{
    /** @var \Magento\Core\Model\Registry  */
    protected $_registry;

    /**
     * @param \Magento\Core\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Helper\Data $coreData
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Helper\Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_registry = $registry;
    }

    /**
     * Retrieve current order model instance
     *
     * @return \Magento\Sales\Model\Order
     */
    private function getOrder()
    {
        return $this->_registry->registry('current_order');
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl(
            $this->getPath(),
            array(
                'order_id' => $this->getOrder()->getId(),
            )
        );
    }

    /**
     * @inheritdoc
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->hasKey()
            && method_exists($this->getOrder(), 'has' . $this->getKey())
            && !$this->getOrder()->{'has' . $this->getKey()}()
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
