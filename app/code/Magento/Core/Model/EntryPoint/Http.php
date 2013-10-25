<?php
/**
 * Http entry point
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Core\Model\EntryPoint;

class Http extends \Magento\Core\Model\AbstractEntryPoint
{
    /**
     * Process http request, output html page or proper information about an exception (if any)
     */
    public function processRequest()
    {
        try {
            parent::processRequest();
        } catch (\Magento\Core\Model\Session\Exception $e) {
            header(
                'Location: ' . $this->_objectManager->get('Magento\Core\Model\StoreManager')->getStore()->getBaseUrl()
            );
        } catch (\Magento\Core\Model\Store\Exception $e) {
            require $this->_objectManager->get('Magento\App\Dir')
                    ->getDir(\Magento\App\Dir::PUB) . DS . 'errors' . DS . '404.php';
        } catch (\Magento\BootstrapException $e) {
            header('Content-Type: text/plain', true, 503);
            echo $e->getMessage();
        } catch (\Exception $e) {
            // attempt to specify store as a skin
            try {
                $storeManager = $this->_objectManager->get('Magento\Core\Model\StoreManager');
                $skin = $storeManager->getStore()->getCode;
            } catch (\Exception $exception) {
                $skin = null;
            }
            $this->_errorHandler->processException($e, array('skin' => $skin));
        }
    }

    /**
     * Run http application
     */
    protected function _processRequest()
    {
        $request = $this->_objectManager->get('Magento\App\RequestInterface');
        $areas = $this->_objectManager->get('Magento\App\AreaList');
        $areaCode = $areas->getCodeByFrontName($request->getFrontName());
        $this->_objectManager->get('Magento\Config\Scope')->setCurrentScope($areaCode);
        $this->_objectManager->configure(
            $this->_objectManager->get('Magento\Core\Model\ObjectManager\ConfigLoader')->load($areaCode)
        );
        $frontController = $this->_objectManager->get('Magento\App\FrontControllerInterface');
        $frontController->dispatch($request);
    }
}
