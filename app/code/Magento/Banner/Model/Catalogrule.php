<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\Banner\Model;

/**
 * Enterprise banner catalog rule model
 *
 * @method \Magento\Banner\Model\Resource\Catalogrule _getResource()
 * @method \Magento\Banner\Model\Resource\Catalogrule getResource()
 * @method int getBannerId()
 * @method \Magento\Banner\Model\Catalogrule setBannerId(int $value)
 * @method int getRuleId()
 * @method \Magento\Banner\Model\Catalogrule setRuleId(int $value)
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Catalogrule extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Initialize promo catalog price rule model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Banner\Model\Resource\Catalogrule');
    }
}
