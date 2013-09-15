<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reward
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reward rate edit form
 *
 * @category    Magento
 * @package     Magento_Reward
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reward\Block\Adminhtml\Reward\Rate\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Getter
     *
     * @return \Magento\Reward\Model\Reward\Rate
     */
    public function getRate()
    {
        return $this->_coreRegistry->registry('current_reward_rate');
    }

    /**
     * Prepare form
     *
     * @return \Magento\Reward\Block\Adminhtml\Reward\Rate\Edit\Form
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id' => 'edit_form',
                'action' => $this->getUrl('*/*/save', array('_current' => true)),
                'method' => 'post',
            ))
        );
        $form->setFieldNameSuffix('rate');
        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend' => __('Reward Exchange Rate Information')
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('website_id', 'select', array(
                'name'   => 'website_id',
                'title'  => __('Website'),
                'label'  => __('Website'),
                'values' => \Mage::getModel('Magento\Reward\Model\Source\Website')->toOptionArray(),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('customer_group_id', 'select', array(
            'name'   => 'customer_group_id',
            'title'  => __('Customer Group'),
            'label'  => __('Customer Group'),
            'values' => \Mage::getModel('Magento\Reward\Model\Source\Customer\Groups')->toOptionArray()
        ));

        $fieldset->addField('direction', 'select', array(
            'name'   => 'direction',
            'title'  => __('Direction'),
            'label'  => __('Direction'),
            'values' => $this->getRate()->getDirectionsOptionArray()
        ));

        $rateRenderer = $this->getLayout()
            ->createBlock('Magento\Reward\Block\Adminhtml\Reward\Rate\Edit\Form\Renderer\Rate')
            ->setRate($this->getRate());
        $direction = $this->getRate()->getDirection();
        if ($direction == \Magento\Reward\Model\Reward\Rate::RATE_EXCHANGE_DIRECTION_TO_CURRENCY) {
            $fromIndex = 'points';
            $toIndex = 'currency_amount';
        } else {
            $fromIndex = 'currency_amount';
            $toIndex = 'points';
        }
        $fieldset->addField('rate_to_currency', 'note', array(
            'title'             => __('Rate'),
            'label'             => __('Rate'),
            'value_index'       => $fromIndex,
            'equal_value_index' => $toIndex
        ))->setRenderer($rateRenderer);

        $form->setUseContainer(true);
        $form->setValues($this->getRate()->getData());
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
