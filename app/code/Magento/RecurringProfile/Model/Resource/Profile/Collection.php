<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Recurring profile collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\RecurringProfile\Model\Resource\Profile;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'recurring_profile_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'recurring_profile_collection';

    /**
     * Entity initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento\RecurringProfile\Model\Profile', 'Magento\RecurringProfile\Model\Resource\Profile');
    }
}
