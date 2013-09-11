<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Rma Item Form Renderer Block for select
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Block\Form\Renderer;

class Select extends \Magento\CustomAttribute\Block\Form\Renderer\Select
{
    /**
     * Prepare rma item attribute
     *
     * @return boolean | \Magento\Rma\Model\Item\Attribute
     */
    public function getAttribute($code)
    {
        /* @var $itemModel  */
        $itemModel = \Mage::getModel('\Magento\Rma\Model\Item');

        /* @var $itemForm \Magento\Rma\Model\Item\Form */
        $itemForm   = \Mage::getModel('\Magento\Rma\Model\Item\Form');
        $itemForm->setFormCode('default')
            ->setStore($this->getStore())
            ->setEntity($itemModel);

        $attribute = $itemForm->getAttribute($code);
        if ($attribute->getIsVisible()) {
            return $attribute;
        }
        return false;
    }
}

