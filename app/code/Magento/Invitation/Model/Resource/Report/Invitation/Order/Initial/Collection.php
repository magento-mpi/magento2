<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Model\Resource\Report\Invitation\Order\Initial;

/**
 * Reports invitation order report collection
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection
    extends \Magento\Reports\Model\Resource\Report\Collection
{
    /**
     * @var string
     */
    protected $_reportCollection = 'Magento\Invitation\Model\Resource\Report\Invitation\Order\Collection';
}
