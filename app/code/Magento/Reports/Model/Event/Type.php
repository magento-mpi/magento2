<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reports
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Event type model
 *
 * @method \Magento\Reports\Model\Resource\Event\Type _getResource()
 * @method \Magento\Reports\Model\Resource\Event\Type getResource()
 * @method string getEventName()
 * @method \Magento\Reports\Model\Event\Type setEventName(string $value)
 * @method int getCustomerLogin()
 * @method \Magento\Reports\Model\Event\Type setCustomerLogin(int $value)
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Reports\Model\Event;

class Type extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magento\Reports\Model\Resource\Event\Type');
    }
}
