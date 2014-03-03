<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Core
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\Resource;

/**
 * Theme resource model
 */
class Theme extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init('core_theme', 'theme_id');
    }
}
