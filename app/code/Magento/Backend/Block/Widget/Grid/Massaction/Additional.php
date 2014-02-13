<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Massaction;

/**
 * Backend widget grid massaction additional action
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Additional extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\View\Layout\Argument\HandlerFactory
     */
    protected $_handlerFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\View\Layout\Argument\HandlerFactory $handlerFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\View\Layout\Argument\HandlerFactory $handlerFactory,
        array $data = array()
    ) {
        parent::__construct($context, $registry, $formFactory, $data);

        $this->_handlerFactory = $handlerFactory;
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
            $argumentHandler = $this->_handlerFactory->getArgumentHandlerByType('options');
            $item['values'] = $argumentHandler->process(array(
                'value' => array(
                    'model' => $item['values']
                )
            ));
        }
        $item['class'] = isset($item['class']) ? $item['class'] . ' absolute-advice' : 'absolute-advice';
    }
}
