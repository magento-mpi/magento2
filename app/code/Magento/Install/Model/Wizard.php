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
namespace Magento\Install\Model;

class Wizard
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
     * @var \Magento\UrlInterface
     */
    protected $_urlBuilder;

    /**
     * Init install wizard
     */
    public function __construct(\Magento\UrlInterface $urlBuilder, \Magento\Install\Model\Config $installConfig)
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
     * @param   \Magento\App\RequestInterface $request
     * @return  \Magento\Object|bool
     */
    public function getStepByRequest(\Magento\App\RequestInterface $request)
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
     * @return  \Magento\Object|bool
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
