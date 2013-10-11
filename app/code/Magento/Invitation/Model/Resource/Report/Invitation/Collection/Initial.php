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
 * Report Reviews collection
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Invitation\Model\Resource\Report\Invitation\Collection;

class Initial extends \Magento\Reports\Model\Resource\Report\Collection
{
    /**
     *  Report sub-collection class name
     *
     * @var string
     */
    protected $_reportCollection = 'Magento\Invitation\Model\Resource\Report\Invitation\Collection';
}
