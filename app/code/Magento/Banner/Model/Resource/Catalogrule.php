<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Banner
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Banner Catalogrule Resource Model
 *
 * @category    Magento
 * @package     Magento_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Model\Resource;

class Catalogrule extends \Magento\Core\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize banner catalog rule resource model
     *
     */
    protected function _construct()
    {
        $this->_init('magento_banner_catalogrule', 'rule_id');
    }
}
