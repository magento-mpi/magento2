<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Review\Block\Form;

/**
 * Review form block
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Configure extends \Magento\Review\Block\Form
{
    /**
     * Get review product id
     *
     * @return int
     */
    public function getProductId()
    {
        return (int)$this->getRequest()->getParam('product_id', false);
    }
}
