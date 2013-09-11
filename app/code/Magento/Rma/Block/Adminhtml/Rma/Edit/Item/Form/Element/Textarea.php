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
 * RMA Item Widget Form Textarea Element Block
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Adminhtml\Rma\Edit\Item\Form\Element;

class Textarea extends \Magento\Data\Form\Element\Textarea
{
    /**
     * Return Form Element HTML
     *
     * @return string
     */
    public function getElementHtml()
    {
        $additionalClasses = \Mage::helper('Magento\Rma\Helper\Eav')
            ->getAdditionalTextElementClasses($this->getEntityAttribute());
        foreach ($additionalClasses as $additionalClass) {
            $this->addClass($additionalClass);
        }
        return parent::getElementHtml();
    }
}
