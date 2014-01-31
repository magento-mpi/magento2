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
 * Backend Inline Translation config factory
 */
namespace Magento\Backend\Model\Translate\Inline;

class ConfigFactory extends \Magento\Translate\Inline\ConfigFactory
{
    /**
     * Get instance of inline translate config
     *
     * @return \Magento\Translate\Inline\ConfigFactory
     */
    public function get()
    {
        return $this->_objectManager->get('Magento\Backend\Model\Translate\Inline\Config');
    }
}
