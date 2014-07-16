<?php
/**
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Install\Controller\Wizard;

class Install extends \Magento\Install\Controller\Wizard
{
    /**
     * Install success callback
     *
     * @return void
     */
    public function installSuccessCallback()
    {
        echo 'parent.installSuccess()';
    }

    /**
     * Install failure callback
     *
     * @return void
     */
    public function installFailureCallback()
    {
        echo 'parent.installFailure()';
    }

    /**
     * Install action
     *
     * @return void
     * @SuppressWarnings(PHPMD.ExitExpression)
     */
    public function execute()
    {
        $pear = \Magento\Framework\Pear::getInstance();
        $params = array('comment' => __("Downloading and installing Magento, please wait...") . "\r\n\r\n");
        if ($this->getRequest()->getParam('do')) {
            $state = $this->getRequest()->getParam('state', 'beta');
            if ($state) {
                $result = $pear->runHtmlConsole(
                    array(
                        'comment' => __("Setting preferred state to: %1", $state) . "\r\n\r\n",
                        'command' => 'config-set',
                        'params' => array('preferred_state', $state)
                    )
                );
                if ($result instanceof PEAR_Error) {
                    $this->installFailureCallback();
                    exit;
                }
            }
            $params['command'] = 'install';
            $params['options'] = array('onlyreqdeps' => 1);
            $params['params'] = $this->_objectManager->get('Magento\Install\Model\Installer\Pear')->getPackages();
            $params['success_callback'] = array($this, 'installSuccessCallback');
            $params['failure_callback'] = array($this, 'installFailureCallback');
        }
        $pear->runHtmlConsole($params);
        $this->getResponse()->clearAllHeaders();
    }
}
