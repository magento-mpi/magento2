<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rma\Block\Form\Renderer;

/**
 * Rma Item Form Renderer Block for select
 */
class Select extends \Magento\CustomAttribute\Block\Form\Renderer\Select
{
    /**
     * @var \Magento\Rma\Model\ItemFactory
     */
    protected $_itemFactory;

    /**
     * @var \Magento\Rma\Model\Item\Form
     */
    protected $_itemFormFactory;

    /**
     * @param \Magento\View\Element\Template\Context $context
     * @param \Magento\Rma\Model\ItemFactory $itemFactory
     * @param \Magento\Rma\Model\Item\FormFactory $itemFormFactory
     * @param array $data
     */
    public function __construct(
        \Magento\View\Element\Template\Context $context,
        \Magento\Rma\Model\ItemFactory $itemFactory,
        \Magento\Rma\Model\Item\FormFactory $itemFormFactory,
        array $data = array()
    ) {
        $this->_itemFactory = $itemFactory;
        $this->_itemFormFactory = $itemFormFactory;
        parent::__construct($context, $data);
        $this->_isScopePrivate = true;
    }

    /**
     * Prepare rma item attribute
     *
     * @param string $code
     * @return bool|\Magento\Rma\Model\Item\Attribute
     */
    public function getAttribute($code)
    {
        /* @var $itemModel  */
        $itemModel = $this->_itemFactory->create();
        /* @var $itemForm \Magento\Rma\Model\Item\Form */
        $itemForm = $this->_itemFormFactory->create();
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

