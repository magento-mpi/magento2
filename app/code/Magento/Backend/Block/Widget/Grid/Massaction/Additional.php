<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend widget grid massaction additional action
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author      Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
namespace Magento\Backend\Block\Widget\Grid\Massaction;

class Additional extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Core\Model\Layout\Argument\HandlerFactory
     */
    protected $_handlerFactory;

    /**
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param \Magento\Core\Model\Layout\Argument\HandlerFactory $handlerFactory
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Registry $registry,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Core\Model\Layout\Argument\HandlerFactory $handlerFactory,
        array $data = array()
    ) {
        parent::__construct($registry, $formFactory, $coreData, $context, $data);

        $this->_handlerFactory = $handlerFactory;
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return \Magento\Backend\Block\Widget\Form
     */
    protected function _prepareForm()
    {
        /** @var Magento_Data_Form $form */
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
     * @param array $item
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
