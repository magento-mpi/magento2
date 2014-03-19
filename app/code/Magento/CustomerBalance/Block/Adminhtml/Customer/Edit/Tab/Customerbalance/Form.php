<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance;

/**
 * Enterprise CustomerBalance
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Form
    extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * @var \Magento\Store\Model\System\Store
     */
    protected $_systemStore;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Data\FormFactory $formFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Store\Model\System\Store $systemStore
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Registry $registry,
        \Magento\Data\FormFactory $formFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Store\Model\System\Store $systemStore,
        array $data = array()
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_systemStore = $systemStore;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form fields
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create();
        $prefix = '_customerbalance';
        $form->setHtmlIdPrefix($prefix);
        $form->setFieldNameSuffix('customerbalance');

        $customer = $this->_customerFactory->create()->load($this->getRequest()->getParam('id'));

        /** @var $fieldset \Magento\Data\Form\Element\Fieldset */
        $fieldset = $form->addFieldset('storecreidt_fieldset',
            array('legend' => __('Update Balance'))
        );

        if (!$this->_storeManager->isSingleStoreMode()) {
            $fieldset->addField('website_id', 'select', array(
                'name'     => 'website_id',
                'label'    => __('Website'),
                'title'    => __('Website'),
                'values'   => $this->_systemStore->getWebsiteValuesForForm(),
                'onchange' => 'updateEmailWebsites()',
            ));
        }

        $fieldset->addField('amount_delta', 'text', array(
            'name'     => 'amount_delta',
            'label'    => __('Update Balance'),
            'title'    => __('Update Balance'),
            'comment'  => __('An amount on which to change the balance'),
        ));

        $fieldset->addField('notify_by_email', 'checkbox', array(
            'name'     => 'notify_by_email',
            'label'    => __('Notify Customer by Email'),
            'title'    => __('Notify Customer by Email'),
            'after_element_html' => !$this->_storeManager->isSingleStoreMode() ? '<script type="text/javascript">'
                . "
                $('{$prefix}notify_by_email').disableSendemail = function() {
                    $('{$prefix}store_id').disabled = (this.checked) ? false : true;
                }.bind($('{$prefix}notify_by_email'));
                Event.observe('{$prefix}notify_by_email', 'click', $('{$prefix}notify_by_email').disableSendemail);
                $('{$prefix}notify_by_email').disableSendemail();
                "
                . '</script>' : '',
        ));

        if (!$this->_storeManager->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'name'  => 'store_id',
                'label' => __('Send Email Notification From the Following Store View'),
                'title' => __('Send Email Notification From the Following Store View'),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        }

        $fieldset->addField('comment', 'text', array(
            'name'     => 'comment',
            'label'    => __('Comment'),
            'title'    => __('Comment'),
            'comment'  => __('Comment'),
        ));

        if ($customer->isReadonly()) {
            if ($form->getElement('website_id')) {
                $form->getElement('website_id')->setReadonly(true, true);
            }
            $form->getElement('store_id')->setReadonly(true, true);
            $form->getElement('amount_delta')->setReadonly(true, true);
            $form->getElement('notify_by_email')->setReadonly(true, true);
        }

        $form->setValues($customer->getData());
        $this->setForm($form);

        return $this;
    }

    /**
     * Processing block html after rendering.
     * Add updateEmailWebsites() logic for multiple store mode
     *
     * @param   string $html
     * @return  string
     */
    protected function _afterToHtml($html)
    {
        $html = parent::_afterToHtml($html);
        if (!$this->_storeManager->isSingleStoreMode()) {
            $block = $this->getLayout()
                ->createBlock('Magento\CustomerBalance\Block\Adminhtml\Customer\Edit\Tab\Customerbalance\Js',
                'customerbalance_edit_js'
            );
            $block->setTemplate('edit/js.phtml');
            $block->setPrefix('_customerbalance');
            $html .= $block->toHtml();
            $html .= '<script type="text/javascript">updateEmailWebsites();</script>';
        }
        return $html;
    }
}
