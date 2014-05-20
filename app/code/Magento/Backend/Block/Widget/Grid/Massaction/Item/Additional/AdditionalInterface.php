<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Grid\Massaction\Item\Additional;

/**
 * Backend grid widget massaction item additional action interface
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
interface AdditionalInterface
{
    /**
     * @param array $configuration
     * @return $this
     */
    public function createFromConfiguration(array $configuration);
}
