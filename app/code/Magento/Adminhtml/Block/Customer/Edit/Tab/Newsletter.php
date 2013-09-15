<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer account form block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Customer\Edit\Tab;

class Newsletter extends \Magento\Backend\Block\Widget\Form\Generic
{
    protected $_template = 'customer/tab/newsletter.phtml';

    public function initForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('_newsletter');
        $customer = $this->_coreRegistry->registry('current_customer');
        $subscriber = Mage::getModel('Magento_Newsletter_Model_Subscriber')->loadByCustomer($customer);
        $this->_coreRegistry->register('subscriber', $subscriber);

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('Newsletter Information')));

        $fieldset->addField('subscription', 'checkbox',
             array(
                    'label' => __('Subscribed to Newsletter'),
                    'name'  => 'subscription'
             )
        );

        if ($customer->isReadonly()) {
            $form->getElement('subscription')->setReadonly(true, true);
        }

        $form->getElement('subscription')->setIsChecked($subscriber->isSubscribed());

        $changedDate = $this->getStatusChangedDate();
        if ($changedDate) {
            $fieldset->addField('change_status_date', 'label', array(
                'label' => $subscriber->isSubscribed() ? __('Last Date Subscribed') : __('Last Date Unsubscribed'),
                'value' => $changedDate,
                'bold'  => true
            ));
        }

        $this->setForm($form);
        return $this;
    }

    public function getStatusChangedDate()
    {
        $subscriber = $this->_coreRegistry->registry('subscriber');
        if($subscriber->getChangeStatusAt()) {
            return $this->formatDate(
                $subscriber->getChangeStatusAt(), Magento_Core_Model_LocaleInterface::FORMAT_TYPE_MEDIUM, true
            );
        }

        return null;
    }

    protected function _prepareLayout()
    {
        $this->setChild('grid',
            $this->getLayout()
                ->createBlock('Magento_Adminhtml_Block_Customer_Edit_Tab_Newsletter_Grid', 'newsletter.grid')
        );
        return parent::_prepareLayout();
    }

}
