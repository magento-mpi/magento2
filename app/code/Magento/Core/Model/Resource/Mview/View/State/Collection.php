<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Core\Model\Resource\Mview\View\State;

class Collection extends \Magento\Core\Model\Resource\Db\Collection\AbstractCollection
    implements \Magento\Mview\View\State\CollectionInterface
{
    /**
     * Collection initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Mview\View\State', 'Magento\Core\Model\Resource\Mview\View\State');
    }
}
