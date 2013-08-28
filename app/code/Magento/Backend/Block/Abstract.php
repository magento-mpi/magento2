<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Backend abstract block
 *
 * @category   Magento
 * @package    Magento_Backend
 * @author     Magento Core Team <core@magentocommerce.com>
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Magento_Backend_Block_Abstract extends Magento_Core_Block_Abstract
{
    /**
     * @var Magento_AuthorizationInterface
     */
    protected $_authorization;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Backend_Block_Context $context
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Backend_Block_Context $context,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
        $this->_authorization = $context->getAuthorization();
    }
}
