<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Cache management form page
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Block_System_Cache_Form extends Mage_Adminhtml_Block_Widget_Form
{
    public function initForm()
    {
        $types = array(
            'config'     => __('Configuration'),
            'layout'     => __('Layouts'),
            'block_html' => __('Blocks HTML output'),
            'eav'        => __('EAV types and attributes'),
            'translate'  => __('Translations'),
            'pear'       => __('PEAR Channels and Packages'),
        );

        $options = array(
            0 => __('Disabled'),
            1 => __('Enabled'),
            2 => __('Clean and Disable'),
            3 => __('Clean and Enable')
        );

        $form = new Varien_Data_Form();

        $fieldset = $form->addFieldset('cache_enable', array(
            'legend'=>__('Cache control')
        ));

        $fieldset->addField('refresh_all_cache', 'checkbox', array(
            'name'=>'refresh[all_cache]',
            'label'=>__('Refresh All Cache'),
            'value'=>1,
        ));

        foreach ($types as $type=>$label) {
            $fieldset->addField('enable_'.$type, 'select', array(
                'name'=>'enable['.$type.']',
                'label'=>$label,
                'value'=>(int)Mage::app()->useCache($type),
                'options'=>$options,
            ));
        }

        $fieldset->addField('refresh_catalog_rewrites', 'checkbox', array(
            'name'=>'refresh[catalog_rewrites]',
            'label'=>__('Refresh Catalog Rewrites'),
            'value'=>1,
        ));

        $this->setForm($form);

        return $this;
    }
}