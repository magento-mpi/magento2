<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Layout Link model class
 *
 * @method int getStoreId()
 * @method int getThemeId()
 * @method int getLayoutUpdateId()
 * @method \Magento\Core\Model\Layout\Link setStoreId($id)
 * @method \Magento\Core\Model\Layout\Link setThemeId($id)
 * @method \Magento\Core\Model\Layout\Link setLayoutUpdateId($id)
 */
namespace Magento\Core\Model\Layout;

class Link extends \Magento\Core\Model\AbstractModel
{
    /**
     * Layout Update model initialization
     */
    protected function _construct()
    {
        $this->_init('Magento\Core\Model\Resource\Layout\Link');
    }
}
