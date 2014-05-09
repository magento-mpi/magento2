<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Adminhtml\Product\Helper\Form;

/**
 * Product form boolean field helper
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Boolean extends \Magento\Framework\Data\Form\Element\Select
{
    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setValues(array(array('label' => __('No'), 'value' => 0), array('label' => __('Yes'), 'value' => 1)));
    }
}
