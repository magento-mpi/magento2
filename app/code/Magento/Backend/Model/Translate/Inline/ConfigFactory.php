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
     * @var \Magento\App\State
     */
    protected $_appState;

    /**
     * @param \Magento\ObjectManager $objectManager
     * @param \Magento\App\State $appState
     */
    public function __construct(\Magento\ObjectManager $objectManager, \Magento\App\State $appState)
    {
        $this->_appState = $appState;
        parent::__construct($objectManager);
    }

    /**
     * Create instance of inline translate config
     *
     * @param string|null $area
     * @return \Magento\Core\Model\Translate\Inline\ConfigInterface
     */
    public function create($area = null)
    {
        if (!isset($area)) {
            $area = $this->_appState->getAreaCode();
        }
        if ($area == \Magento\Backend\App\Area\FrontNameResolver::AREA_CODE) {
            return $this->_objectManager->create('Magento\Backend\Model\Translate\Inline\Config');
        }

        return parent::create();
    }
}
