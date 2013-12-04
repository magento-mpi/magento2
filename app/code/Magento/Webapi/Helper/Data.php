<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Webapi\Helper;

use Magento\Integration\Controller\Adminhtml\Integration as IntegrationController;

class Data extends \Magento\App\Helper\AbstractHelper
{
    /** @var \Magento\Core\Model\Registry */
    protected $_registry;

    public function __construct(
        \Magento\App\Helper\Context $context,
        \Magento\Core\Model\Registry $registry
    ) {
        $this->_registry = $registry;
        parent::__construct($context);
    }

    public function getSelectedResources()
    {
        $selectedResourceIds = array();
        $currentIntegration = $this->_registry->registry(IntegrationController::REGISTRY_KEY_CURRENT_INTEGRATION);
        if ($currentIntegration
            && isset($currentIntegration['resource']) && is_array($currentIntegration['resource'])
        ) {
            $selectedResourceIds = $currentIntegration['resource'];
        }
        return $selectedResourceIds;
    }
}
