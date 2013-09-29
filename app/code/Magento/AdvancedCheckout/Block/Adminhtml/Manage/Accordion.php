<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Accordion for different product sources for adding to shopping cart
 *
 * @category   Magento
 * @package    Magento_AdvancedCheckout
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\AdvancedCheckout\Block\Adminhtml\Manage;

class Accordion extends \Magento\Adminhtml\Block\Widget\Accordion
{
    /**
     * Add accordion items based on layout updates
     */
    protected function _toHtml()
    {
        if (!$this->_authorization->isAllowed('Magento_AdvancedCheckout::update')) {
            return parent::_toHtml();
        }
        $layout = $this->getLayout();
        /** @var $child \Magento\Core\Block\AbstractBlock  */
        foreach ($layout->getChildBlocks($this->getNameInLayout()) as $child) {
            $name = $child->getNameInLayout();
            $data = array(
                'title'       => $child->getHeaderText(),
                'open'        => false
            );
            if ($child->hasData('open')) {
                $data['open'] = $child->getData('open');
            }
            if ($child->hasData('content_url')) {
                $data['content_url'] = $child->getData('content_url');
            } else {
                $data['content'] = $layout->renderElement($name);
            }
            $this->addItem($name, $data);
        }

        return parent::_toHtml();
    }
}
