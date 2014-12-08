<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\RecurringPayment\Block\Payment;

/**
 * Recurring payment view
 */
class View extends \Magento\Framework\View\Element\Template
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
    protected $_info = [];

    /**
     * Related orders collection
     *
     * @var \Magento\Sales\Model\Resource\Order\Collection|null
     */
    protected $_relatedOrders = null;

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_registry;

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'recurring/payment/view/info.phtml';

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
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
     * @param \Magento\Framework\Object $row
     * @return string
     */
    public function renderRowValue(\Magento\Framework\Object $row)
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
     * @param string|null $key
     * @return void
     */
    protected function _addInfo(array $data, $key = null)
    {
        $object = new \Magento\Framework\Object($data);
        if ($key) {
            $this->_info[$key] = $object;
        } else {
            $this->_info[] = $object;
        }
    }

    /**
     * Get current payment from registry and assign store/locale information to it
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $this->_recurringPayment = $this->_registry->registry(
            'current_recurring_payment'
        )->setStore(
            $this->_storeManager->getStore()
        );
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
                        ['payment' => $this->_recurringPayment->getId()]
                    )
                );
            }
        }

        return parent::_toHtml();
    }
}
