<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Reports invitation order report collection
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Invitation\Model\Resource\Report\Invitation\Order\Initial;

class Collection
    extends \Magento\Reports\Model\Resource\Report\Collection
{
    /*
     * Report sub-collection class name
     * @var string
     */
    protected $_reportCollection = '\Magento\Invitation\Model\Resource\Report\Invitation\Order\Collection';
}
