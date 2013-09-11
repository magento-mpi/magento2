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
 * Enterprise banner catalog rule model
 *
 * @method \Magento\Banner\Model\Resource\Catalogrule _getResource()
 * @method \Magento\Banner\Model\Resource\Catalogrule getResource()
 * @method int getBannerId()
 * @method \Magento\Banner\Model\Catalogrule setBannerId(int $value)
 * @method int getRuleId()
 * @method \Magento\Banner\Model\Catalogrule setRuleId(int $value)
 *
 * @category    Magento
 * @package     Magento_Banner
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Banner\Model;

class Catalogrule extends \Magento\Core\Model\AbstractModel
{
    /**
     * Initialize promo catalog price rule model
     *
     */
    protected function _construct()
    {
        $this->_init('\Magento\Banner\Model\Resource\Catalogrule');
    }
}
