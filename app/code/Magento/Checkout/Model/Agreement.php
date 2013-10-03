<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @method \Magento\Checkout\Model\Resource\Agreement _getResource()
 * @method \Magento\Checkout\Model\Resource\Agreement getResource()
 * @method string getName()
 * @method \Magento\Checkout\Model\Agreement setName(string $value)
 * @method string getContent()
 * @method \Magento\Checkout\Model\Agreement setContent(string $value)
 * @method string getContentHeight()
 * @method \Magento\Checkout\Model\Agreement setContentHeight(string $value)
 * @method string getCheckboxText()
 * @method \Magento\Checkout\Model\Agreement setCheckboxText(string $value)
 * @method int getIsActive()
 * @method \Magento\Checkout\Model\Agreement setIsActive(int $value)
 * @method int getIsHtml()
 * @method \Magento\Checkout\Model\Agreement setIsHtml(int $value)
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Checkout\Model;

class Agreement extends \Magento\Core\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('Magento\Checkout\Model\Resource\Agreement');
    }
}
