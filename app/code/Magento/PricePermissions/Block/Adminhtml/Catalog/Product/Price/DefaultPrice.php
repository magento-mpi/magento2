<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PricePermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Default Product Price field renderer
*
 * @category    Magento
 * @package     Magento_PricePermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\PricePermissions\Block\Adminhtml\Catalog\Product\Price;

class DefaultPrice
    extends \Magento\Backend\Block\System\Config\Form\Field
{
    /**
     * Price permissions data
     *
     * @var Magento_PricePermissions_Helper_Data
     */
    protected $_pricePermissionsData = null;

    /**
     * @param Magento_PricePermissions_Helper_Data $pricePermissionsData
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Template_Context $context
     * @param Magento_Core_Model_App $application
     * @param array $data
     */
    public function __construct(
        Magento_PricePermissions_Helper_Data $pricePermissionsData,
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Template_Context $context,
        Magento_Core_Model_App $application,
        array $data = array()
    ) {
        $this->_pricePermissionsData = $pricePermissionsData;
        parent::__construct($coreData, $context, $application, $data);
    }

    /**
     * Render Default Product Price field as disabled if user does not have enough permissions
     *
     * @param \Magento\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Data\Form\Element\AbstractElement $element)
    {
        if (!$this->_pricePermissionsData->getCanAdminEditProductPrice()) {
            $element->setReadonly(true, true);
        }
        return parent::_getElementHtml($element);
    }
}
