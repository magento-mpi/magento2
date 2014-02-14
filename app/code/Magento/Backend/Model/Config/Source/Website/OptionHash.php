<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Backend\Model\Config\Source\Website;

use Magento\Core\Model\System\Store;

class OptionHash
    implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * System Store Model
     *
     * @var Store
     */
    protected $_systemStore;

    /**
     * @param Store $systemStore
     */
    public function __construct(Store $systemStore)
    {
        $this->_systemStore = $systemStore;
    }

    /**
     * Return websites array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->_systemStore->getWebsiteOptionHash();
    }
}


