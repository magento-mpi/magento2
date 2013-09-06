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
 * Adminhtml Review Edit Form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Review_Edit_Form extends Magento_Backend_Block_Widget_Form_Generic
{
    /**
     * Review data
     *
     * @var Magento_Review_Helper_Data
     */
    protected $_reviewData = null;

    /**
     * @param Magento_Review_Helper_Data $reviewData
     * @param Magento_Data_Form_Factory $formFactory
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Review_Helper_Data $reviewData,
        Magento_Data_Form_Factory $formFactory,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        array $data = array()
    ) {
        $this->_reviewData = $reviewData;
        parent::__construct($formFactory, $coreData, $context, $data);
    }

    protected function _prepareForm()
    {
        $review = Mage::registry('review_data');
        $product = Mage::getModel('Magento_Catalog_Model_Product')->load($review->getEntityPkValue());
        $customer = Mage::getModel('Magento_Customer_Model_Customer')->load($review->getCustomerId());

        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create(array(
            'id'        => 'edit_form',
            'action'    => $this->getUrl('*/*/save', array(
                'id' => $this->getRequest()->getParam('id'),
                'ret' => Mage::registry('ret')
            )),
            'method'    => 'post'
        ));

        $fieldset = $form->addFieldset('review_details', array(
            'legend' => __('Review Details'),
            'class' => 'fieldset-wide'
        ));

        $fieldset->addField('product_name', 'note', array(
            'label'     => __('Product'),
            'text'      => '<a href="' . $this->getUrl('*/catalog_product/edit', array('id' => $product->getId()))
                . '" onclick="this.target=\'blank\'">' . $this->_reviewData->escapeHtml($product->getName()) . '</a>'
        ));

        if ($customer->getId()) {
            $customerText = __('<a href="%1" onclick="this.target=\'blank\'">%2 %3</a> <a href="mailto:%4">(%4)</a>',
                $this->getUrl('*/customer/edit', array('id' => $customer->getId(), 'active_tab'=>'review')),
                $this->escapeHtml($customer->getFirstname()),
                $this->escapeHtml($customer->getLastname()),
                $this->escapeHtml($customer->getEmail()));
        } else {
            if (is_null($review->getCustomerId())) {
                $customerText = __('Guest');
            } elseif ($review->getCustomerId() == 0) {
                $customerText = __('Administrator');
            }
        }

        $fieldset->addField('customer', 'note', array(
            'label'     => __('Posted By'),
            'text'      => $customerText,
        ));

        $fieldset->addField('summary_rating', 'note', array(
            'label'     => __('Summary Rating'),
            'text'      => $this->getLayout()->createBlock('Magento_Adminhtml_Block_Review_Rating_Summary')->toHtml(),
        ));

        $fieldset->addField('detailed_rating', 'note', array(
            'label'     => __('Detailed Rating'),
            'required'  => true,
            'text'      => '<div id="rating_detail">'
                           . $this->getLayout()->createBlock('Magento_Adminhtml_Block_Review_Rating_Detailed')->toHtml()
                           . '</div>',
        ));

        $fieldset->addField('status_id', 'select', array(
            'label'     => __('Status'),
            'required'  => true,
            'name'      => 'status_id',
            'values'    => $this->_reviewData->getReviewStatusesOptionArray(),
        ));

        /**
         * Check is single store mode
         */
        if (!Mage::app()->hasSingleStore()) {
            $field = $fieldset->addField('select_stores', 'multiselect', array(
                'label'     => __('Visible In'),
                'required'  => true,
                'name'      => 'stores[]',
                'values'    => Mage::getSingleton('Magento_Core_Model_System_Store')->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()
                ->createBlock('Magento_Backend_Block_Store_Switcher_Form_Renderer_Fieldset_Element');
            $field->setRenderer($renderer);
            $review->setSelectStores($review->getStores());
        } else {
            $fieldset->addField('select_stores', 'hidden', array(
                'name'      => 'stores[]',
                'value'     => Mage::app()->getStore(true)->getId()
            ));
            $review->setSelectStores(Mage::app()->getStore(true)->getId());
        }

        $fieldset->addField('nickname', 'text', array(
            'label'     => __('Nickname'),
            'required'  => true,
            'name'      => 'nickname'
        ));

        $fieldset->addField('title', 'text', array(
            'label'     => __('Summary of Review'),
            'required'  => true,
            'name'      => 'title',
        ));

        $fieldset->addField('detail', 'textarea', array(
            'label'     => __('Review'),
            'required'  => true,
            'name'      => 'detail',
            'style'     => 'height:24em;',
        ));

        $form->setUseContainer(true);
        $form->setValues($review->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
