<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Resource\Rma;

/**
 * RMA entity collection
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Collection extends \Magento\Model\Resource\Db\Collection\AbstractCollection
{
    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Rma\Model\Rma', 'Magento\Rma\Model\Resource\Rma');
    }
}
