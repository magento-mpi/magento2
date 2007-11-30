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
class Mage_Adminhtml_Block_Extensions_Local_Edit_Tab_Abstract extends Mage_Adminhtml_Block_Widget_Form
{
    protected $_addRowButtonHtml;
    protected $_removeRowButtonHtml;

    public function __construct()
    {
        parent::__construct();
        $this->setData(Mage::getSingleton('adminhtml/session')->getLocalExtensionPackageFormData());
    }

    public function initForm()
    {
        return $this;
    }

    public function getValue($key, $default='')
    {
        $value = $this->getData($key);
        return htmlspecialchars($value ? $value : $default);
    }

    public function getSelected($key, $value)
    {
        return $this->getData($key)==$value ? 'selected' : '';
    }

    public function getChecked($key)
    {
        return $this->getData($key) ? 'checked' : '';
    }

    public function getAddRowButtonHtml($container, $template, $title='Add')
    {
        if (!isset($this->_addRowButtonHtml[$container])) {
            $this->_addRowButtonHtml[$container] = $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('add')
                    ->setLabel($this->__($title))
                    ->setOnClick("addRow('".$container."', '".$template."')")
                    ->toHtml();
        }
        return $this->_addRowButtonHtml[$container];
    }

    public function getRemoveRowButtonHtml()
    {
        if (!$this->_removeRowButtonHtml) {
            $this->_removeRowButtonHtml = $this->getLayout()
                ->createBlock('adminhtml/widget_button')
                    ->setType('button')
                    ->setClass('delete')
                    ->setLabel($this->__('Remove'))
                    ->setOnClick("removeRow(this)")
                    ->toHtml();
        }
        return $this->_removeRowButtonHtml;
    }
}
