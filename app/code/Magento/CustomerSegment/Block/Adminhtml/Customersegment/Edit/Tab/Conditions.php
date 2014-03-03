<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Conditions tab of customer segment configuration
 *
 * @category    Magento
 * @package     Magento_CustomerSegment
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit\Tab;

class Conditions
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Backend\Block\Widget\Form\Renderer\Fieldset
     */
    protected $_fieldset;

    /**
     * @var \Magento\Rule\Block\Conditions
     */
    protected $_conditions;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $fieldset
     * @param \Magento\Rule\Block\Conditions $conditions
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Backend\Block\Widget\Form\Renderer\Fieldset $fieldset,
        \Magento\Rule\Block\Conditions $conditions,
        array $data = array()
    ) {
        $this->_fieldset = $fieldset;
        $this->_conditions = $conditions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare conditions form
     *
     * @return \Magento\CustomerSegment\Block\Adminhtml\Customersegment\Edit\Tab\Conditions
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_customer_segment');

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();

        $form->setHtmlIdPrefix('segment_');
        $params = array('apply_to' => $model->getApplyTo());
        $url = $this->getUrl('customersegment/index/newConditionHtml/form/segment_conditions_fieldset', $params);

        $renderer = $this->_fieldset->setTemplate('Magento_CatalogRule::promo/fieldset.phtml')
            ->setNewChildUrl($url);

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend' => __('Conditions'),
            'class' => 'fieldset',
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer($this->_conditions);

        if (\Magento\CustomerSegment\Model\Segment::APPLY_TO_VISITORS_AND_REGISTERED == $model->getApplyTo()) {
            $fieldset->addField('conditions-label', 'label', array(
                'note' => __('* applicable to visitors and registered customers'),
            ));
        }

        $form->setValues($model->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
