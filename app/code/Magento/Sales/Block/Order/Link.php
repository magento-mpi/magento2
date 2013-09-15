<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales order link
 */
class Magento_Sales_Block_Order_Link extends Magento_Page_Block_Link_Current
{
    /** @var Magento_Core_Model_Registry  */
    protected $_registry;

    /**
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Helper_Data $coreData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Helper_Data $coreData,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_registry = $registry;
    }

    /**
     * Retrieve current order model instance
     *
     * @return Magento_Sales_Model_Order
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
