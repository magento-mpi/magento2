<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Converts cron parameters from XML files
 */
class Magento_Cron_Model_Config_Converter_Xml implements Magento_Config_ConverterInterface
{
    /**
     * Converting data to array type
     *
     * @param mixed $source
     * @return array
     * @throws LogicException
     */
    public function convert($source)
    {
        $output = array();

        if (!$source instanceof DOMDocument) {
            return $output;
        }

        /** @var DOMNodeList $jobs */
        $jobs = $source->getElementsByTagName('job');
        /** @var DOMElement $jobConfig */
        foreach ($jobs as $jobConfig) {
            $jobName = $jobConfig->getAttribute('name');

            if (!$jobName) {
                throw new InvalidArgumentException('Attribute "name" does not exist');
            }
            $config['name'] = $jobName;
            $config += $this->_convertCronConfig($jobConfig);

            /** @var DOMText $schedules */
            foreach ($jobConfig->childNodes as $schedules) {
                if ($schedules->nodeName == 'schedule') {
                    if (!empty($schedules->nodeValue)) {
                        $config['schedule'] = $schedules->nodeValue;
                        break;
                    }
                }
                continue;
            }
            $output[$jobName] = $config;
        }
        return $output;
    }

    /**
     * Convert specific cron configurations
     *
     * @param DOMElement $jobConfig
     * @return array
     * @throws InvalidArgumentException
     */
    protected function _convertCronConfig($jobConfig)
    {
        $instanceName = $jobConfig->getAttribute('instance');
        $methodName = $jobConfig->getAttribute('method');

        if (!isset($instanceName)) {
            throw new InvalidArgumentException('Attribute "instance" does not exist');
        }
        if (!isset($methodName)) {
            throw new InvalidArgumentException('Attribute "method" does not exist');
        }
        return array('instance' => $instanceName, 'method' => $methodName);
    }
}
