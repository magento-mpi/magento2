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
class Magento_Adminhtml_Block_System_Cache_Form extends Magento_Adminhtml_Block_Widget_Form
{
    public function __construct(Magento_Backend_Block_Template_Context $context, array $data = array())
    {
        parent::__construct($context, $data);
    }

    /**
     * Initialize cache management form
     *
     * @return Magento_Adminhtml_Block_System_Cache_Form
     */
    public function initForm()
    {
        $form = new Magento_Data_Form();

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

        /** @var $cacheState Magento_Core_Model_Cache_StateInterface */
        $cacheState = Mage::getObjectManager()->get('Magento_Core_Model_Cache_StateInterface');

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
