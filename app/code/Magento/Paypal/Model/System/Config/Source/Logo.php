<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Source model for available logo types
 */
namespace Magento\Paypal\Model\System\Config\Source;

class Logo implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Paypal\Model\ConfigFactory
     */
    protected $_configFactory;

    /**
     * @param \Magento\Paypal\Model\ConfigFactory $configFactory
     */
    public function __construct(\Magento\Paypal\Model\ConfigFactory $configFactory)
    {
        $this->_configFactory = $configFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        $result = array('' => __('No Logo'));
        $result += $this->_configFactory->create()->getAdditionalOptionsLogoTypes();
        return $result;
    }
}
