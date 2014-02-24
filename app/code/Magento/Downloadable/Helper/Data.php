<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Downloadable\Helper;

use Magento\Downloadable\Model\Link\Purchased\Item;

/**
 * Downloadable helper
 *
 * @category    Magento
 * @package     Magento_Downloadable
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Data extends \Magento\App\Helper\AbstractHelper
{
    /**
     * Core store config
     *
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_coreStoreConfig;

    /**
     * @param \Magento\App\Helper\Context $context
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     */
    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\Store\Config $coreStoreConfig
    ) {
        $this->_coreStoreConfig = $coreStoreConfig;
        parent::__construct($context);
    }

    /**
     * Check is link shareable or not
     *
     * @param \Magento\Downloadable\Model\Link|Item $link
     * @return bool
     */
    public function getIsShareable($link)
    {
        $shareable = false;
        switch ($link->getIsShareable()) {
            case \Magento\Downloadable\Model\Link::LINK_SHAREABLE_YES:
            case \Magento\Downloadable\Model\Link::LINK_SHAREABLE_NO:
                $shareable = (bool) $link->getIsShareable();
                break;
            case \Magento\Downloadable\Model\Link::LINK_SHAREABLE_CONFIG:
                $shareable = (bool) $this->_coreStoreConfig->getConfigFlag(\Magento\Downloadable\Model\Link::XML_PATH_CONFIG_IS_SHAREABLE);
        }
        return $shareable;
    }
}
