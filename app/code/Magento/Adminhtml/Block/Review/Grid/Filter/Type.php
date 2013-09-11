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
 * Adminhtml review grid filter by type
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Review\Grid\Filter;

class Type extends \Magento\Adminhtml\Block\Widget\Grid\Column\Filter\Select
{
    protected function _getOptions()
    {
        return array(
              array('label'=>'', 'value'=>''),
              array('label'=>__('Administrator'), 'value'=>1),
              array('label'=>__('Customer'), 'value'=>2),
              array('label'=>__('Guest'), 'value'=>3)
        );
    }

    public function getCondition()
    {
        if ($this->getValue() == 1) {
            return 1;
        } elseif ($this->getValue() == 2) {
            return 2;
        } else {
            return 3;
        }
    }
}// Class \Magento\Adminhtml\Block\Review\Grid\Filter\Type END
