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
 * Invitation status history collection
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Invitation\Model\Resource\Invitation\History;

class Collection
    extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Intialize collection
     *
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\Invitation\Model\Invitation\History',
            'Magento\Invitation\Model\Resource\Invitation\History'
        );
    }
}
