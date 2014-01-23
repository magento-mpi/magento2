<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal Express Onepage checkout block
 *
 * @category   Magento
 * @package    Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Paypal\Block\Express\Review;

class Details extends \Magento\Checkout\Block\Cart\Totals
{
    protected $_address;

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
     * Return review shipping address
     *
     * @return \Magento\Sales\Model\Order\Address
     */
    public function getAddress()
    {
        if (empty($this->_address)) {
            $this->_address = $this->getQuote()->getShippingAddress();
        }
        return $this->_address;
    }

    /**
     * Return review quote totals
     *
     * @return array
     */
    public function getTotals()
    {
        return $this->getQuote()->getTotals();
    }
}
