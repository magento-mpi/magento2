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
     * @param \DOMDocument $source
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
                $config += $this->convertCronConfig($jobConfig);
                $config += $this->convertCronSchedule($jobConfig);

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
    protected function convertCronConfig(\DOMElement $jobConfig)
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

    /**
     * Convert schedule cron configurations
     *
     * @param $jobConfig
     * @return array
     */
    protected function convertCronSchedule(\DOMElement $jobConfig)
    {
        $result = array();
        /** @var \DOMText $schedules */
        foreach ($jobConfig->childNodes as $schedules) {
            if ($schedules->nodeName == 'schedule') {
                if (!empty($schedules->nodeValue)) {
                    $result['schedule'] = $schedules->nodeValue;
                    break;
                }
            }
            continue;
        }

        return $result;
    }
}
