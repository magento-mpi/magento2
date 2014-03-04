<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Block\Widget\Grid\Massaction;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Additional extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\View\Layout\Argument\Interpreter\Options
     */
    protected $_optionsInterpreter;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\View\Layout\Argument\Interpreter\Options $optionsInterpreter
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\View\Layout\Argument\Interpreter\Options $optionsInterpreter,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_optionsInterpreter = $optionsInterpreter;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        foreach ($this->getData('fields') as $itemId => $item) {
            $this->_prepareFormItem($item);
            $form->addField($itemId, $item['type'], $item);
        }
        $this->setForm($form);
        return $this;
    }

    /**
     * Prepare form item
     *
     * @param array &$item
     * @return void
     */
    protected function _prepareFormItem(array &$item)
    {
        if ($item['type'] == 'select' && is_string($item['values'])) {
            $modelClass = $item['values'];
            $item['values'] = $this->_optionsInterpreter->evaluate(array('model' => $modelClass));
        }
        $item['class'] = isset($item['class']) ? $item['class'] . ' absolute-advice' : 'absolute-advice';
    }
}
