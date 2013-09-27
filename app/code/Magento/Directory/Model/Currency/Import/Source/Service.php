<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Directory_Model_Currency_Import_Source_Service implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * @var Magento_Core_Model_Config
     */
    private $_importConfig;

    /**
     * @var array
     */
    private $_options;

    /**
     * @param Magento_Directory_Model_Currency_Import_Config $importConfig
     */
    public function __construct(Magento_Directory_Model_Currency_Import_Config $importConfig)
    {
        $this->_importConfig = $importConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        if ($this->_options === null) {
            $this->_options = array();
            foreach ($this->_importConfig->getAvailableServices() as $serviceName) {
                $this->_options[] = array(
                    'label' => $this->_importConfig->getServiceLabel($serviceName),
                    'value' => $serviceName,
                );
            }
        }
        return $this->_options;
    }
}
