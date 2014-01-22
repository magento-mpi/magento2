<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Resource authorizenet debug collection model
 *
 * @category    Magento
 * @package     Magento_Authorizenet
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Authorizenet\Model\Resource\Authorizenet\Debug;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Resource initialization
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\Authorizenet\Model\Authorizenet\Debug',
            'Magento\Authorizenet\Model\Resource\Authorizenet\Debug'
        );
    }
}
