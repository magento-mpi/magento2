<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Controller\Adminhtml\Category;

use Magento\Framework\View\Element\BlockInterface;

/**
 * Catalog category widgets controller for CMS WYSIWYG
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Widget extends \Magento\Backend\App\Action
{
    /**
     * @return BlockInterface
     */
    protected function _getCategoryTreeBlock()
    {
        return $this->_view->getLayout()->createBlock(
            'Magento\Catalog\Block\Adminhtml\Category\Widget\Chooser',
            '',
            array(
                'data' => array(
                    'id' => $this->getRequest()->getParam('uniq_id'),
                    'use_massaction' => $this->getRequest()->getParam('use_massaction', false)
                )
            )
        );
    }
}
