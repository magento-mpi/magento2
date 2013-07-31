<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml cms pages content block
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Cms_Block_Adminhtml_Cms_Page extends Magento_Adminhtml_Block_Template
{
    /**
     * Add  column Versioned to cms page grid
     *
     * @return Enterprise_Cms_Block_Adminhtml_Cms_Page
     */
    protected function _prepareLayout()
    {
        /* @var $pageGrid Magento_Adminhtml_Block_Cms_Page_Grid */
        $page = $this->getLayout()->getBlock('cms_page');
        if ($page) {
            $pageGrid = $page->getChildBlock('grid');
            if($pageGrid) {
                $pageGrid->addColumnAfter('versioned', array(
                    'index'     => 'under_version_control',
                    'header'    => Mage::helper('Enterprise_Cms_Helper_Data')->__('Version Control'),
                    'width'     => 10,
                    'type'      => 'options',
                    'options'   => array(Mage::helper('Enterprise_Cms_Helper_Data')->__('No'),
                        Mage::helper('Enterprise_Cms_Helper_Data')->__('Yes')
                    )
                ), 'page_actions');
            }
        }

        return $this;
    }
}
