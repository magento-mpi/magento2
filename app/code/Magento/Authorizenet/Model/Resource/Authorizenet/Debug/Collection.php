<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Authorizenet\Model\Resource\Authorizenet\Debug;

/**
 * Resource Authorize.net debug collection model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\Authorizenet\Model\Authorizenet\Debug',
            'Magento\Authorizenet\Model\Resource\Authorizenet\Debug'
        );
    }
}
