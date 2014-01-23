<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout order review
 */
namespace Magento\Checkout\Block\Onepage\Review;

class Info extends \Magento\Sales\Block\Items\AbstractItems
{
    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Checkout\Model\Session $checkoutSession,
        array $data = array()
    ) {
        $this->_checkoutSession = $checkoutSession;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve item renderer block
     *
     * @param string $type
     * @return \Magento\View\Element\AbstractBlock
     * @throws \RuntimeException
     */
    public function getItemRenderer($type)
    {
        /** @var \Magento\View\Element\RendererList $rendererList */
        $rendererList = $this->getRendererListName()
            ? $this->getLayout()->getBlock($this->getRendererListName())
            : $this->getChildBlock('renderer.list');
        if (!$rendererList) {
            throw new \RuntimeException('Renderer list fo block "' . $this->getNameInLayout() . '" is not defined');
        }
        $renderer = $rendererList->getRenderer($type) ?: $rendererList->getRenderer(self::DEFAULT_TYPE);
        if (!$renderer instanceof \Magento\View\Element\BlockInterface) {
            throw new \RuntimeException('Renderer for type "' . $type . '" does not exist.');
        }
        if ($this->getRendererTemplate()) {
            $renderer->setTemplate($this->getRendererTemplate());
        }
        $renderer->setRenderedBlock($this);
        return $renderer;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->_checkoutSession->getQuote()->getAllVisibleItems();
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        return $this->_checkoutSession->getQuote()->getTotals();
    }
}
