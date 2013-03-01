<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_DesignEditor
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Design mode design editor url model
 */
class Mage_DesignEditor_Model_Url_DesignMode extends Mage_Core_Model_Url
{
    /**
     * Build url by requested path and parameters
     *
     * @param string|null $routePath
     * @param array|null $routeParams
     * @return string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getUrl($routePath = null, $routeParams = null)
    {
        /** @todo ACB temporary solution */
        if (isset($routeParams['_useRealRoute'])) {
            $useRealRoute = (bool)$routeParams['_useRealRoute'];
            unset($routeParams['_useRealRoute']);
            if ($useRealRoute) {
                return parent::getUrl($routePath, $routeParams);
            }
        }
        return '#';
    }

    /**
     * @param array $routeParams
     * @return string
     */
    /** @todo ACB temporary solution */
    public function getRoutePath($routeParams = array())
    {
        if (isset($routeParams['_useVdeFrontend'])) {
            $useVdeFrontend = (bool)$routeParams['_useVdeFrontend'];
            unset($routeParams['_useVdeFrontend']);
            if ($useVdeFrontend) {
                $vdeFrontName = Mage::getObjectManager()->get('Mage_DesignEditor_Helper_Data')->getFrontName();

                return $vdeFrontName . "/" . parent::getRoutePath($routeParams);
            }
        }

        return parent::getRoutePath($routeParams);
    }
}
