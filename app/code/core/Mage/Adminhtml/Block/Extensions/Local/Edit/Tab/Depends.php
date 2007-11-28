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
 * Convert profile edit tab
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Adminhtml_Block_Extensions_Local_Edit_Tab_Depends
    extends Mage_Adminhtml_Block_Extensions_Local_Edit_Tab_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('extensions/local/depends.phtml');
    }

    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_depends');

        $fieldset = $form->addFieldset('depends_php_fieldset', array('legend'=>__('PHP Version')));

    	$fieldset->addField('depends_php_min', 'text', array(
            'name' => 'depends_php_min',
            'label' => __('Minimum'),
            'required' => true,
        ));

    	$fieldset->addField('depends_php_max', 'text', array(
            'name' => 'depends_php_max',
            'label' => __('Maximum'),
            'required' => true,
        ));

    	$fieldset->addField('depends_php_recommended', 'text', array(
            'name' => 'depends_php_recommended',
            'label' => __('Recommended'),
        ));

    	$fieldset->addField('depends_php_exclude', 'text', array(
            'name' => 'depends_php_exclude',
            'label' => __('Exclude (comma separated)'),
        ));

        #$form->setValues($model->getData());

        $this->setForm($form);

        return $this;
    }

    public function getPackages()
    {
        return array('Mage_Core'=>'Mage_Core');
    }

    public function getPackageDepends()
    {
        return array();
    }

    public function getSubpackageDepends()
    {
        return array();
    }

    public function getExtensions()
    {
        $arr = array();
        foreach (get_loaded_extensions() as $ext) {
            $arr[$ext] = $ext;
        }
        asort($arr, SORT_STRING);
        return $arr;
    }

    public function getExtensionDepends()
    {
        return array();
    }

    public function getMaintainerLevels()
    {
        return array(
            'lead'=>__('Lead'),
            'developer'=>__('Developer'),
            'contributor'=>__('Contributor'),
            'helper'=>__('Helper'),
        );
    }

    public function getDependLevels()
    {
        return array(
            'required'=>__('Required'),
            'optional'=>__('Optional'),
            'conflicts'=>__('Conflicts'),
        );
    }
}
