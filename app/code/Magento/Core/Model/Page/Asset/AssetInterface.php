<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Interface of a resource linked to a page
 */
namespace Magento\Core\Model\Page\Asset;

interface AssetInterface
{
    /**
     * Retrieve URL pointing to a resource
     *
     * @return string
     */
    public function getUrl();

    /**
     * Retrieve type of contents
     *
     * @return string
     */
    public function getContentType();
}
