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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Connect
 * @subpackage  Adminhtml
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Block for Dependencies
 *
 * @category    Mage
 * @package     Mage_Connect
 * @subpackage  Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Depends
    extends Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Abstract
{

    /**
    * Constructor, sets default template
    */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('connect/extension/custom/depends.phtml');
    }

    /**
    * Create object for form
    *
    * @return Mage_Connect_Block_Adminhtml_Extension_Custom_Edit_Tab_Depends
    */
    public function initForm()
    {
        $form = new Varien_Data_Form();
        $form->setHtmlIdPrefix('_depends');

        $fieldset = $form->addFieldset('depends_php_fieldset', array('legend' => Mage::helper('connect')->__('PHP Version')));

        $fieldset->addField('depends_php_min', 'text', array(
            'name' => 'depends_php_min',
            'label' => Mage::helper('connect')->__('Minimum'),
            'required' => true,
            'value' => '5.2.0',
        ));

        $fieldset->addField('depends_php_max', 'text', array(
            'name' => 'depends_php_max',
            'label' => Mage::helper('connect')->__('Maximum'),
            'required' => true,
            'value' => '5.2.20',
        ));

        $form->setValues($this->getData());

        $this->setForm($form);

        return $this;
    }

    /**
    * Retrieve list of loaded PHP extensions
    *
    * @return array
    */
    public function getExtensions()
    {
        $arr = array();
        foreach (get_loaded_extensions() as $ext) {
            $arr[$ext] = $ext;
        }
        asort($arr, SORT_STRING);
        return $arr;
    }

}
