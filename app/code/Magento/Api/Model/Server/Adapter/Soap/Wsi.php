<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Api
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * SOAP WS-I compatible adapter
 *
 * @category   Magento
 * @package    Magento_Api
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Api\Model\Server\Adapter\Soap;

class Wsi extends \Magento\Api\Model\Server\Adapter\Soap
{
    /**
     * Run webservice
     *
     * @param \Magento\Api\Controller\Action $controller
     * @return \Magento\Api\Model\Server\Adapter\Soap
     */
    public function run()
    {
        $apiConfigCharset = \Mage::getStoreConfig("api/config/charset");

        if ($this->getController()->getRequest()->getParam('wsdl') !== null) {
            /** @var $wsdlConfig \Magento\Api\Model\Wsdl\Config */
            $wsdlConfig = \Mage::getModel('\Magento\Api\Model\Wsdl\Config');
            $wsdlConfig->setHandler($this->getHandler())
                ->setCacheId('wsdl_config_global_soap_wsi')
                ->init();
            $this->getController()->getResponse()
                ->clearHeaders()
                ->setHeader('Content-Type', 'text/xml; charset=' . $apiConfigCharset)
                ->setBody(
                preg_replace(
                    '/(\>\<)/i',
                    ">\n<",
                    str_replace(
                        '<soap:operation soapAction=""></soap:operation>',
                        "<soap:operation soapAction=\"\" />\n",
                        str_replace(
                            '<soap:body use="literal"></soap:body>',
                            "<soap:body use=\"literal\" />\n",
                            preg_replace(
                                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                                '<?xml version="$1" encoding="' . $apiConfigCharset . '"?>',
                                $wsdlConfig->getWsdlContent()
                            )
                        )
                    )
                )
            );
        } else {
            try {
                $this->_instantiateServer();

                $content = preg_replace(
                    '/(\>\<)/i',
                    ">\n<",
                    str_replace(
                        '<soap:operation soapAction=""></soap:operation>',
                        "<soap:operation soapAction=\"\" />\n",
                        str_replace(
                            '<soap:body use="literal"></soap:body>',
                            "<soap:body use=\"literal\" />\n",
                            preg_replace(
                                '/<\?xml version="([^\"]+)"([^\>]+)>/i',
                                '<?xml version="$1" encoding="' . $apiConfigCharset . '"?>',
                                $this->_soap->handle()
                             )
                         )
                    )
                );

                $this->getController()->getResponse()
                    ->clearHeaders()
                    ->setHeader('Content-Type', 'text/xml; charset=' . $apiConfigCharset)
                    ->setHeader('Content-Length', strlen($content), true)
                    ->setBody($content);
            } catch (\Zend_Soap_Server_Exception $e) {
                $this->fault($e->getCode(), $e->getMessage());
            } catch (\Exception $e) {
                $this->fault($e->getCode(), $e->getMessage());
            }
        }

        return $this;
    }
}
