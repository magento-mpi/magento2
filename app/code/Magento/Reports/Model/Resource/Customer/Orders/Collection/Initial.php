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
 * Customers by orders Report collection
 *
 * @category    Magento
 * @package     Magento_Reports
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Reports\Model\Resource\Customer\Orders\Collection;

class Initial extends \Magento\Reports\Model\Resource\Report\Collection
{
    /**
     * Report sub-collection class name
     * @var string
     */
    protected $_reportCollection = 'Magento\Reports\Model\Resource\Customer\Orders\Collection';
}
