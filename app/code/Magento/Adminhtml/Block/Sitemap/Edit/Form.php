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
 * Sitemap edit form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sitemap\Edit;

class Form extends \Magento\Adminhtml\Block\Widget\Form
{

    /**
     * Init form
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sitemap_form');
        $this->setTitle(__('Sitemap Information'));
    }


    protected function _prepareForm()
    {
        $model = \Mage::registry('sitemap_sitemap');

        $form = new \Magento\Data\Form(array(
            'id'        => 'edit_form',
            'action'    => $this->getData('action'),
            'method'    => 'post'
        ));

        $fieldset = $form->addFieldset('add_sitemap_form', array('legend' => __('Sitemap')));

        if ($model->getId()) {
            $fieldset->addField('sitemap_id', 'hidden', array(
                'name' => 'sitemap_id',
            ));
        }

        $fieldset->addField('sitemap_filename', 'text', array(
            'label' => __('Filename'),
            'name'  => 'sitemap_filename',
            'required' => true,
            'note'  => __('example: sitemap.xml'),
            'value' => $model->getSitemapFilename()
        ));

        $fieldset->addField('sitemap_path', 'text', array(
            'label' => __('Path'),
            'name'  => 'sitemap_path',
            'required' => true,
            'note'  => __('example: "sitemap/" or "/" for base path (path must be writeable)'),
            'value' => $model->getSitemapPath()
        ));

        if (!\Mage::app()->hasSingleStore()) {
            $field = $fieldset->addField('store_id', 'select', array(
                'label'    => __('Store View'),
                'title'    => __('Store View'),
                'name'     => 'store_id',
                'required' => true,
                'value'    => $model->getStoreId(),
                'values'   => \Mage::getSingleton('Magento\Core\Model\System\Store')->getStoreValuesForForm(),
            ));
            $renderer = $this->getLayout()->createBlock('\Magento\Backend\Block\Store\Switcher\Form\Renderer\Fieldset\Element');
            $field->setRenderer($renderer);
        }
        else {
            $fieldset->addField('store_id', 'hidden', array(
                'name'     => 'store_id',
                'value'    => \Mage::app()->getStore(true)->getId()
            ));
            $model->setStoreId(\Mage::app()->getStore(true)->getId());
        }

        $form->setValues($model->getData());

        $form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
