<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid column block that is displayed only in multistore mode
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Ui\Listing\Block\Column;

class Multistore extends \Magento\Ui\Listing\Block\Column
{
    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(\Magento\Backend\Block\Template\Context $context, array $data = array())
    {
        parent::__construct($context, $data);
    }

    /**
     * Get header css class name
     *
     * @return string
     */
    public function isDisplayed()
    {
        return !$this->_storeManager->isSingleStoreMode();
    }
}
