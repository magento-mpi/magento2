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

/**
 * Admin OptionHash will include the default store (Admin) with the OptionHash.
 *
 * This class is needed until the layout file supports supplying arguments to an option model.
 */
class AdminOptionHash extends OptionHash
{
    /**
     * @param Store $systemStore
     * @param bool $withDefaultWebsite
     */
    public function __construct(Store $systemStore, $withDefaultWebsite = true)
    {
        parent::__construct($systemStore, $withDefaultWebsite);
    }
}
