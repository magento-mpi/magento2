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
 * Value object carrying page properties
 */
namespace Magento\Core\Model;

class Page
{
    /**
     * @var \Magento\Core\Model\Page\Asset\Collection
     */
    private $_assets;

    /**
     * @param \Magento\Core\Model\Page\Asset\Collection $assets
     */
    public function __construct(\Magento\Core\Model\Page\Asset\Collection $assets)
    {
        $this->_assets = $assets;
    }

    /**
     * Retrieve collection of assets linked to a page
     *
     * @return \Magento\Core\Model\Page\Asset\Collection
     */
    public function getAssets()
    {
        return $this->_assets;
    }
}
