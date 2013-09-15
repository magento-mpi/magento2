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
 * Cache management form page
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\System\Cache;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Initialize cache management form
     *
     * @return \Magento\Adminhtml\Block\System\Cache\Form
     */
    public function initForm()
    {
        /** @var Magento_Data_Form $form */
        $form = $this->_formFactory->create();

        $fieldset = $form->addFieldset('cache_enable', array(
            'legend' => __('Cache Control')
        ));

        $fieldset->addField('all_cache', 'select', array(
            'name'=>'all_cache',
            'label'=>'<strong>'.__('All Cache').'</strong>',
            'value'=>1,
            'options'=>array(
                '' => __('No change'),
                'refresh' => __('Refresh'),
                'disable' => __('Disable'),
                'enable' => __('Enable'),
            ),
        ));

        /** @var $cacheState \Magento\Core\Model\Cache\StateInterface */
        $cacheState = \Mage::getObjectManager()->get('Magento\Core\Model\Cache\StateInterface');

        foreach ($this->_coreData->getCacheTypes() as $type=>$label) {
            $fieldset->addField('enable_'.$type, 'checkbox', array(
                'name'=>'enable['.$type.']',
                'label'=>__($label),
                'value'=>1,
                'checked'=>(int)$cacheState->isEnabled($type),
                //'options'=>$options,
            ));
        }

        $this->setForm($form);

        return $this;
    }
}
