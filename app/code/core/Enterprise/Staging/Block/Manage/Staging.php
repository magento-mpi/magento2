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
 * @category   Enterprise
 * @package    Enterprise_Staging
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Staging manage stagings block
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Staging_Block_Manage_Staging extends Mage_Adminhtml_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('enterprise/staging/manage/staging.phtml');
    }

    protected function _prepareLayout()
    {
        $this->setChild('add_new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('enterprise_staging')->__('Create Staging'),
                    'onclick'   => "setLocation('".$this->getUrl('*/*/edit')."')",
                    'class'   => 'add'
                    ))
                );

        $this->setChild('grid', $this->getLayout()->createBlock('enterprise_staging/manage_staging_grid', 'staging.grid'));
        return parent::_prepareLayout();
    }

    public function getAddNewButtonHtml()
    {
        if ($this->_enabledAddNewButton()) {
            return $this->getChildHtml('add_new_button');
        } else {
            return '';
        }
    }

    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }

    protected function _enabledAddNewButton()
    {
        return true;
    }
}