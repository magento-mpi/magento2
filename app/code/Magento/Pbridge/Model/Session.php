<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Session model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Model;

class Session extends \Magento\Core\Model\Session\AbstractSession
{
    /**
     * @param \Magento\Core\Model\Session\Context $context
     * @param array $data
     * @param null $sessionName
     */
    public function __construct(\Magento\Core\Model\Session\Context $context, array $data = array(), $sessionName = null)
    {
        parent::__construct($context, $data);
        $this->init('magento_pbridge', $sessionName);
    }
}
