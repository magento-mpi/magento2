<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product form boolean field helper
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Catalog\Product\Helper\Form;

class Boolean extends \Magento\Data\Form\Element\Select
{
    protected function _construct()
    {
        parent::_construct();
        $this->setValues(array(
            array(
                'label' => __('No'),
                'value' => 0,
            ),
            array(
                'label' => __('Yes'),
                'value' => 1,
            ),
        ));
    }
}
