<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
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
 * @license    http://www.magentocommerce.com/license/enterprise-edition
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

    /**
     * Prepare Layout
     */
    protected function _prepareLayout()
    {
        $this->setChild('add_new_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('enterprise_staging')->__('Create Staging Website'),
                    'onclick'   => "setLocation('".$this->getUrl('*/*/edit')."')",
                    'class'   => 'add'
                    ))
                );

        $this->setChild('grid', $this->getLayout()
            ->createBlock('enterprise_staging/manage_staging_grid', 'staging.grid'));
        return parent::_prepareLayout();
    }

    /**
     * return "add new" button as html
     *
     * @return string
     */
    public function getAddNewButtonHtml()
    {
        return $this->getChildHtml('add_new_button');
    }

    /**
     * Return grid html url
     */
    public function getGridHtml()
    {
        return $this->getChildHtml('grid');
    }
}
