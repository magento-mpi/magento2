<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Invitation\Model\Resource\Invitation\History;

/**
 * Invitation status history collection
 *
 * @category    Magento
 * @package     Magento_Invitation
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Collection
    extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Intialize collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(
            'Magento\Invitation\Model\Invitation\History',
            'Magento\Invitation\Model\Resource\Invitation\History'
        );
    }
}
