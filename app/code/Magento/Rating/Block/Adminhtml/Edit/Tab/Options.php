<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rating
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rating\Block\Adminhtml\Edit\Tab;

class Options extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Rating option factory
     *
     * @var \Magento\Rating\Model\Rating\OptionFactory
     */
    protected $_optionFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Rating\Model\Rating\OptionFactory $optionFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Rating\Model\Rating\OptionFactory $optionFactory,
        array $data = array()
    ) {
        $this->_optionFactory = $optionFactory;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form   = $this->_formFactory->create();

        $fieldset = $form->addFieldset('options_form', array('legend'=>__('Assigned Options')));

        if ($this->_coreRegistry->registry('rating_data')) {
            $collection = $this->_optionFactory->create()
                ->getResourceCollection()
                ->addRatingFilter($this->_coreRegistry->registry('rating_data')->getId())
                ->load();

            $i = 1;
            foreach ($collection->getItems() as $item) {
                $fieldset->addField('option_code_' . $item->getId() , 'text', array(
                    'label'     => __('Option Label'),
                    'required'  => true,
                    'name'      => 'option_title[' . $item->getId() . ']',
                    'value'     => ( $item->getCode() ) ? $item->getCode() : $i,
                ));
                $i ++;
            }
        } else {
            for ($i = 1; $i <= 5; $i++) {
                $fieldset->addField('option_code_' . $i, 'text', array(
                    'label'     => __('Option Title'),
                    'required'  => true,
                    'name'      => 'option_title[add_' . $i . ']',
                    'value'     => $i,
                ));
            }
        }

        $this->setForm($form);
        return parent::_prepareForm();
    }

}
