<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Install
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Installation wizard model
 */
class Magento_Install_Model_Wizard
{
    /**
     * Wizard configuration
     *
     * @var array
     */
    protected $_steps = array();

    /**
     * Url builder
     *
     * @var Magento_Core_Model_UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Init install wizard
     */
    public function __construct(Magento_Core_Model_UrlInterface $urlBuilder, Magento_Install_Model_Config $installConfig)
    {
        $this->_steps = $installConfig->getWizardSteps();
        $this->_urlBuilder = $urlBuilder;
        $this->_initSteps();
    }

    protected function _initSteps()
    {
        foreach (array_keys($this->_steps) as $index) {
            $this->_steps[$index]->setUrl(
                $this->_getUrl($this->_steps[$index]->getController(), $this->_steps[$index]->getAction())
            );

            if (isset($this->_steps[$index + 1])) {
                $this->_steps[$index]->setNextUrl(
                    $this->_getUrl($this->_steps[$index + 1]->getController(), $this->_steps[$index + 1]->getAction())
                );
                $this->_steps[$index]->setNextUrlPath(
                    $this->_getUrlPath(
                        $this->_steps[$index + 1]->getController(),
                        $this->_steps[$index + 1]->getAction()
                    )
                );
            }
            if (isset($this->_steps[$index - 1])) {
                $this->_steps[$index]->setPrevUrl(
                    $this->_getUrl($this->_steps[$index - 1]->getController(), $this->_steps[$index - 1]->getAction())
                );
                $this->_steps[$index]->setPrevUrlPath(
                    $this->_getUrlPath(
                        $this->_steps[$index - 1]->getController(),
                        $this->_steps[$index - 1]->getAction()
                    )
                );
            }
        }
    }

    /**
     * Get wizard step by request
     *
     * @param   Zend_Controller_Request_Abstract $request
     * @return  Magento_Object|bool
     */
    public function getStepByRequest(Zend_Controller_Request_Abstract $request)
    {
        foreach ($this->_steps as $step) {
            if ($step->getController() == $request->getControllerName()
                    && $step->getAction() == $request->getActionName()) {
                return $step;
            }
        }
        return false;
    }

    /**
     * Get wizard step by name
     *
     * @param   string $name
     * @return  Magento_Object|bool
     */
    public function getStepByName($name)
    {
        foreach ($this->_steps as $step) {
            if ($step->getName() == $name) {
                return $step;
            }
        }
        return false;
    }

    /**
     * Get all wizard steps
     *
     * @return array
     */
    public function getSteps()
    {
        return $this->_steps;
    }

    /**
     * Get url
     *
     * @param string $controller
     * @param string $action
     * @return string
     */
    protected function _getUrl($controller, $action)
    {
        return $this->_urlBuilder->getUrl($this->_getUrlPath($controller, $action));
    }

    /**
     * Retrieve Url Path
     *
     * @param string $controller
     * @param string $action
     * @return string
     */
    protected function _getUrlPath($controller, $action)
    {
        return 'install/' . $controller . '/' . $action;
    }
}
