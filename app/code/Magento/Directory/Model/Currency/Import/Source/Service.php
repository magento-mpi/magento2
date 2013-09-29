<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Directory\Model\Currency\Import\Source;

class Service implements \Magento\Core\Model\Option\ArrayInterface
{
    /**
     * @var \Magento\Core\Model\Config
     */
    private $_importConfig;

    /**
     * @var array
     */
    private $_options;

    /**
     * @param \Magento\Directory\Model\Currency\Import\Config $importConfig
     */
    public function __construct(\Magento\Directory\Model\Currency\Import\Config $importConfig)
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
