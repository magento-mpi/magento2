<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Banner\Model\Resource;

/**
 * Banner Catalogrule Resource Model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Catalogrule extends \Magento\Framework\Model\Resource\Db\AbstractDb
{
    /**
     * Initialize banner catalog rule resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('magento_banner_catalogrule', 'rule_id');
    }
}
