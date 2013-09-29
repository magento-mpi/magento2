<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Block\Recurring\Profile\View;

/**
 * Recurring profile view reference
 */
class Reference extends \Magento\Sales\Block\Recurring\Profile\View
{
    /**
     * Prepare reference info
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $this->_shouldRenderInfo = true;
        foreach (array('method_code', 'reference_id', 'schedule_description', 'state') as $key) {
            $this->_addInfo(array(
                'label' => $this->_profile->getFieldLabel($key),
                'value' => $this->_profile->renderData($key),
            ));
        }
    }
}
