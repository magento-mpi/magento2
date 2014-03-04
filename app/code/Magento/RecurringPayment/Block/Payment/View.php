<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring payment view
 */
namespace Magento\RecurringPayment\Block\Payment;

class View extends \Magento\View\Element\Template
{
    /**
     * @var \Magento\RecurringPayment\Model\Payment
     */
    protected $_recurringPayment = null;

    /**
     * Whether the block should be used to render $_info
     *
     * @var bool
     */
    protected $_shouldRenderInfo = false;

    /**
     * Information to be rendered
     *
     * @var array
     */
    protected $_info = array();

    /**
     * Related orders collection
     *
     * @var \Magento\Sales\Model\Resource\Order\Collection
     */
    protected $_relatedOrders = null;

    /**
     * @var \Magento\Registry
     */
    protected $_registry;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'recurring/payment/view/info.phtml';

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Registry $registry,
        array $data = array()
    ) {
        $this->_registry = $registry;
        parent::__construct($context, $data);

    }

    /**
     * Getter for rendered info, if any
     *
     * @return array
     */
    public function getRenderedInfo()
    {
        return $this->_info;
    }

    /**
     * Get rendered row value
     *
     * @param \Magento\Object $row
     * @return string
     */
    public function renderRowValue(\Magento\Object $row)
    {
        $value = $row->getValue();
        if (is_array($value)) {
            $value = implode("\n", $value);
        }
        if (!$row->getSkipHtmlEscaping()) {
            $value = $this->escapeHtml($value);
        }
        return nl2br($value);
    }

    /**
     * Add specified data to the $_info
     *
     * @param array $data
     * @param string $key = null
     */
    protected function _addInfo(array $data, $key = null)
    {
        $object = new \Magento\Object($data);
        if ($key) {
            $this->_info[$key] = $object;
        } else {
            $this->_info[] = $object;
        }
    }

    /**
     * Get current payment from registry and assign store/locale information to it
     */
    protected function _prepareLayout()
    {
        $this->_recurringPayment = $this->_registry->registry('current_recurring_payment')
            ->setStore($this->_storeManager->getStore());
        return parent::_prepareLayout();
    }

    /**
     * Render self only if needed, also render info tabs group if needed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_recurringPayment || $this->_shouldRenderInfo && !$this->_info) {
            return '';
        }

        if ($this->hasShouldPrepareInfoTabs()) {
            $layout = $this->getLayout();
            foreach ($this->getGroupChildNames('info_tabs') as $name) {
                $block = $layout->getBlock($name);
                if (!$block) {
                    continue;
                }
                $block->setViewUrl(
                    $this->getUrl(
                        "*/*/{$block->getViewAction()}",
                        array('payment' => $this->_recurringPayment->getId())
                    )
                );
            }
        }

        return parent::_toHtml();
    }
}
