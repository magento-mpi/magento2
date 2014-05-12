<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Block for grid with packages.
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Connect\Block\Adminhtml\Extension\Custom\Edit\Tab;

class Load extends \Magento\Backend\Block\Template
{
    /**
     * Retrieve Grid Block HTML
     *
     * @return string
     */
    public function getPackageGridHtml()
    {
        return $this->getChildHtml('local_package_grid');
    }
}
