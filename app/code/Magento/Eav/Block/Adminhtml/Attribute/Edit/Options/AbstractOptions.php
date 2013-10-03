<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Attribute add/edit form options tab
 *
 * @category   Magento
 * @package    Magento_Eav
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Eav\Block\Adminhtml\Attribute\Edit\Options;

abstract class AbstractOptions extends \Magento\Core\Block\AbstractBlock
{
    /**
     * Preparing layout, adding buttons
     *
     * @return \Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\AbstractOptions
     */
    protected function _prepareLayout()
    {
        $this->addChild(
            'labels',
            'Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Labels'
        );
        $this->addChild(
            'options',
            'Magento\Eav\Block\Adminhtml\Attribute\Edit\Options\Options'
        );
        return parent::_prepareLayout();
    }

    /**
     * @inheritdoc
     * @return string
     */
    protected function _toHtml()
    {
        return $this->getChildHtml();
    }
}
