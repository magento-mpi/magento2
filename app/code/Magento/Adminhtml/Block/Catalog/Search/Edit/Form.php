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
 * Adminhtml tag edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Catalog\Search\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Init Form properties
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('catalog_search_form');
        $this->setTitle(__('Search Information'));
    }

    /**
     * Prepare form fields
     *
     * @return \Magento\Adminhtml\Block\Catalog\Search\Edit\Form
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('current_catalog_search');
        /* @var $model Magento_CatalogSearch_Model_Query */

        /** @var \Magento\Data\Form $form */
        $form = $this->_formFactory->create(array(
            'attributes' => array(
                'id'        => 'edit_form',
                'action'    => $this->getData('action'),
                'method' => 'post',
            ))
        );

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>__('General Information')));

        $yesno = array(
            array(
                'value' => 0,
                'label' => __('No')
            ),
            array(
                'value' => 1,
                'label' => __('Yes')
            ));

        if ($model->getId()) {
            $fieldset->addField('query_id', 'hidden', array(
                'name'      => 'query_id',
            ));
        }

        $fieldset->addField('query_text', 'text', array(
            'name'      => 'query_text',
            'label'     => __('Search Query'),
            'title'     => __('Search Query'),
            'required'  => true,
        ));

        if (!\Mage::app()->isSingleStoreMode()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'name'      => 'store_id',
                'label'     => __('Store'),
                'title'     => __('Store'),
                'values'    => \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm(true, false),
                'required'  => true,
            ));
            $renderer = $this->getLayout()->createBlock('Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'      => 'store_id'
            ));
            $model->setStoreId(\Mage::app()->getStore(true)->getId());
        }

        if ($model->getId()) {
            $fieldset->addField('num_results', 'text', array(
                'name'     => 'num_results',
                'label'    => __('Number of results'),
                'title'    => __('Number of results (For the last time placed)'),
                'note'     => __('For the last time placed.'),
                'required' => true,
            ));

            $fieldset->addField('popularity', 'text', array(
                'name'     => 'popularity',
                'label'    => __('Number of Uses'),
                'title'    => __('Number of Uses'),
                'required' => true,
            ));
        }

        $fieldset->addField('synonym_for', 'text', array(
            'name'  => 'synonym_for',
            'label' => __('Synonym For'),
            'title' => __('Synonym For'),
            'note'  => __('Will make search for the query above return results for this search'),
        ));

        $fieldset->addField('redirect', 'text', array(
            'name'  => 'redirect',
            'label' => __('Redirect URL'),
            'title' => __('Redirect URL'),
            'class' => 'validate-url',
            'note'  => __('ex. http://domain.com'),
        ));

        $fieldset->addField('display_in_terms', 'select', array(
            'name'   => 'display_in_terms',
            'label'  => __('Display in Suggested Terms'),
            'title'  => __('Display in Suggested Terms'),
            'values' => $yesno,
        ));

        $form->setValues($model->getData());
        $form->setUseContainer(true);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}
