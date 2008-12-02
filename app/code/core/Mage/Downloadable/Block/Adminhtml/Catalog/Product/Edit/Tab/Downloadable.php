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
 * @package     Mage_Downloadable
 * @copyright   Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Adminhtml catalog product downloadable items tab and form
 *
 * @category    Mage
 * @package     Mage_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Downloadable_Block_Adminhtml_Catalog_Product_Edit_Tab_Downloadable extends Mage_Adminhtml_Block_Widget implements Mage_Adminhtml_Block_Widget_Tab_Interface
{
    protected $_product = null;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('downloadable/product/edit/downloadable.phtml');
    }

    public function getTabUrl()
    {
        return $this->getUrl('downloadable/product_edit/form', array('_current' => true));
    }

    public function getTabClass()
    {
        return 'ajax';
    }

    public function getTabLabel()
    {
        return Mage::helper('downloadable')->__('Downloadable Information');
    }
    public function getTabTitle()
    {
        return Mage::helper('downloadable')->__('Downloadable Information');
    }
    public function canShowTab()
    {
        return true;
    }
    public function isHidden()
    {
        return false;
    }

    protected function _toHtml()
    {
        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->setId('downloadableInfo');


        $accordion->addItem('samples', array(
            'title'   => Mage::helper('adminhtml')->__('Samples'),
            'content' => 'and this probably should not be opened by default', // $this->getLayout()->createBlock('...')->toHtml() . '<br />',
            'open'    => false
        ));

        $accordion->addItem('links', array(
            'title'   => Mage::helper('adminhtml')->__('Links'),
            'content' => 'assume this is the primary content', // $this->getLayout()->createBlock('...')->toHtml(),
            'open'    => true
        ));

        $this->setChild('accordion', $accordion);

        return parent::_toHtml();
    }
}
