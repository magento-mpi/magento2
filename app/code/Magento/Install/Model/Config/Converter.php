<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Install_Model_Config_Converter implements Magento_Config_ConverterInterface
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function convert($source)
    {
        $xpath = new DOMXPath($source);

        $steps = array();
        /** @var $step DOMNode */
        foreach ($xpath->query('/install_wizard/steps/step') as $step) {
            $stepAttributes = $step->attributes;
            $id = $stepAttributes->getNamedItem('id')->nodeValue;
            $steps[$id]['name'] = $id;

            $controller = $stepAttributes->getNamedItem('controller')->nodeValue;
            $steps[$id]['controller'] = $controller;

            $action = $stepAttributes->getNamedItem('action')->nodeValue;
            $steps[$id]['action'] = $action;

            /** @var $child DOMNode */
            foreach ($step->childNodes as $child) {
                if ($child->nodeName == 'label') {
                    $steps[$id]['code'] = $child->nodeValue;
                }
            }
        }

        $writables = array();
        $notWritables = array();
        /** @var $step DOMNode */
        foreach ($xpath->query('/install_wizard/filesystem_prerequisites/directory') as $directory) {
            $directoryAttributes = $directory->attributes;
            $alias = $directoryAttributes->getNamedItem('alias')->nodeValue;
            $existence = $directoryAttributes->getNamedItem('existence')->nodeValue;
            $recursive = $directoryAttributes->getNamedItem('recursive')->nodeValue;
            if ($directoryAttributes->getNamedItem('writable')->nodeValue == 'true') {
                $writables[$alias]['existence'] = $existence == 'true' ? '1' : '0';
                $writables[$alias]['recursive'] = $recursive == 'true' ? '1' : '0';
            } else {
                $notWritables[$alias]['existence'] = $existence == 'true' ? '1' : '0';
                $notWritables[$alias]['recursive'] = $recursive == 'true' ? '1' : '0';
            }
        }

        return array(
            'steps' => $steps,
            'filesystem_prerequisites' => array(
                'writables' => $writables,
                'notWritables' => $notWritables
            )
        );
    }
}
