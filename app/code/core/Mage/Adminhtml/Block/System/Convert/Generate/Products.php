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
 * @author     Moshe Gurvich <dmitriy@varien.com>
 */
class Mage_Adminhtml_Block_System_Convert_Generate_Products extends Mage_Adminhtml_Block_Widget_Container
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('system/convert/generate/products.phtml');
    }

    public function getAttributes()
    {
        $entityTypeId = Mage::getSingleton('eav/config')->getEntityType('catalog_product')->getId();
        return Mage::getResourceModel('eav/entity_attribute_collection')
            ->setEntityTypeFilter($entityTypeId)->load()->getIterator();
    }

    public function getStores()
    {
        return Mage::getConfig()->getNode('stores')->children();
    }

    public function getBackButtonHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
            ->setClass('back')->setLabel($this->__('Back'))
            ->setOnClick('setLocation(\'' . $this->getUrl('*/system_convert_profile') .'\')')
            ->toHtml();
    }

    public function getGenerateButtonHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setType('submit')
            ->setClass('add')->setLabel($this->__('Generate Profile'))
            ->toHtml();
    }

    public function getAddMapButtonHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
            ->setClass('add')->setLabel($this->__('Add Field Mapping'))
            ->setOnClick("addFieldMapping()")->toHtml();
    }

    public function getRemoveMapButtonHtml()
    {
        return $this->getLayout()->createBlock('adminhtml/widget_button')->setType('button')
            ->setClass('delete')->setLabel($this->__('Remove'))
            ->setOnClick("removeFieldMapping(this)")->toHtml();
    }
}
