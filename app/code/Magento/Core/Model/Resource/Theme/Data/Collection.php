<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource\Theme\Data;

/**
 * Theme data collection
 */
class Collection extends \Magento\Core\Model\Resource\Theme\Collection implements
    \Magento\View\Design\Theme\Label\ListInterface,
    \Magento\View\Design\Theme\ListInterface
{
    /**
     * @inheritdoc
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Theme\Data', 'Magento\Core\Model\Resource\Theme');
    }
}
