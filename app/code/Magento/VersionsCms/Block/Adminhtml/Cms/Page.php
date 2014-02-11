<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\VersionsCms\Block\Adminhtml\Cms;

/**
 * Adminhtml cms pages content block
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Page extends \Magento\Backend\Block\Template
{
    /**
     * Add  column Versioned to cms page grid
     *
     * @return \Magento\VersionsCms\Block\Adminhtml\Cms\Page
     */
    protected function _prepareLayout()
    {
        /* @var $pageGrid \Magento\Cms\Block\Adminhtml\Page\Grid */
        $page = $this->getLayout()->getBlock('cms_page');
        if ($page) {
            $pageGrid = $page->getChildBlock('grid');
            if($pageGrid) {
                $pageGrid->addColumnAfter('versioned', array(
                    'index'     => 'under_version_control',
                    'header'    => __('Version Control'),
                    'width'     => 10,
                    'type'      => 'options',
                    'options'   => array(__('No'),
                        __('Yes')
                    )
                ), 'page_actions');
            }
        }

        return $this;
    }
}
