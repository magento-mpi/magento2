<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Cron
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Cron\Model\Config\Converter;

/**
 * Converts cron parameters from XML files
 */
class Xml implements \Magento\Config\ConverterInterface
{
    /**
     * Converting data to array type
     *
     * @param mixed $source
     * @return array
     * @throws \InvalidArgumentException
     */
    public function convert($source)
    {
        $output = array();

        if (!$source instanceof \DOMDocument) {
            return $output;
        }

        $groups = $source->getElementsByTagName('group');
        foreach ($groups as $group) {
            /** @var $group \DOMElement */
            if (!$group->hasAttribute('id')) {
                throw new \InvalidArgumentException('Attribute "id" does not exist');
            }
            /** @var \DOMElement $jobConfig */
            foreach ($group->childNodes as $jobConfig) {
                if ($jobConfig->nodeName != 'job') {
                    continue;
                }
                $jobName = $jobConfig->getAttribute('name');

                if (!$jobName) {
                    throw new \InvalidArgumentException('Attribute "name" does not exist');
                }
                $config = array();
                $config['name'] = $jobName;
                $config += $this->_convertCronConfig($jobConfig);

                /** @var \DOMText $schedules */
                foreach ($jobConfig->childNodes as $schedules) {
                    if ($schedules->nodeName == 'schedule') {
                        if (!empty($schedules->nodeValue)) {
                            $config['schedule'] = $schedules->nodeValue;
                            break;
                        }
                    }
                    continue;
                }
                $output[$group->getAttribute('id')][$jobName] = $config;
            }
        }
        return $output;
    }

    /**
     * Convert specific cron configurations
     *
     * @param \DOMElement $jobConfig
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function _convertCronConfig($jobConfig)
    {
        $instanceName = $jobConfig->getAttribute('instance');
        $methodName = $jobConfig->getAttribute('method');

        if (!isset($instanceName)) {
            throw new \InvalidArgumentException('Attribute "instance" does not exist');
        }
        if (!isset($methodName)) {
            throw new \InvalidArgumentException('Attribute "method" does not exist');
        }
        return array('instance' => $instanceName, 'method' => $methodName);
    }
}
