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

class ConfigFactory extends \Magento\Core\Model\Translate\Inline\ConfigFactory
{
    /**
     * Create instance of inline translate config
     *
     * @param string|null $area
     * @return \Magento\Core\Model\Translate\Inline\ConfigInterface
     */
    public function create($area = null)
    {
        return $this->_objectManager->create('Magento\Backend\Model\Translate\Inline\Config');
    }
}
