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
 * description
 *
 * @category    Magento
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Promo\Catalog\Edit\Tab;

class Conditions
    extends \Magento\Adminhtml\Block\Widget\Form
    implements \Magento\Adminhtml\Block\Widget\Tab\TabInterface
{
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return __('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return __('Conditions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm()
    {
        $model = \Mage::registry('current_promo_catalog_rule');

        //$form = new \Magento\Data\Form(array('id' => 'edit_form1', 'action' => $this->getData('action'), 'method' => 'post'));
        $form = new \Magento\Data\Form();

        $form->setHtmlIdPrefix('rule_');

        $renderer = \Mage::getBlockSingleton('Magento\Adminhtml\Block\Widget\Form\Renderer\Fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/promo_catalog/newConditionHtml/form/rule_conditions_fieldset'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>__('Conditions (leave blank for all products)'))
        )->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => __('Conditions'),
            'title' => __('Conditions'),
            'required' => true,
        ))->setRule($model)->setRenderer(\Mage::getBlockSingleton('Magento\Rule\Block\Conditions'));
/*
        $fieldset = $form->addFieldset('actions_fieldset', array('legend'=>__('Actions')));

        $fieldset->addField('actions', 'text', array(
            'name' => 'actions',
            'label' => __('Actions'),
            'title' => __('Actions'),
            'required' => true,
        ))->setRule($model)->setRenderer(\Mage::getBlockSingleton('Magento\Rule\Block\Actions'));

        $fieldset = $form->addFieldset('options_fieldset', array('legend'=>__('Options')));

        $fieldset->addField('stop_rules_processing', 'select', array(
            'label'     => __('Stop Further Rules Processing'),
            'title'     => __('Stop Further Rules Processing'),
            'name'      => 'stop_rules_processing',
            'required' => true,
            'options'    => array(
                '1' => __('Yes'),
                '0' => __('No'),
            ),
        ));
*/
        $form->setValues($model->getData());

        //$form->setUseContainer(true);

        $this->setForm($form);

        return parent::_prepareForm();
    }
}
