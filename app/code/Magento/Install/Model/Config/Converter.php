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

        $result = array(
            'steps' => array(),
            'filesystem_prerequisites' => array(
                'writables' => array(),
                'notWritables' => array()
            )
        );

        /** @var $step DOMNode */
        foreach ($xpath->query('/install_wizard/steps/step') as $step) {
            $stepAttributes = $step->attributes;
            $id = $stepAttributes->getNamedItem('id')->nodeValue;
            $result['steps'][$id]['name'] = $id;

            $controller = $stepAttributes->getNamedItem('controller')->nodeValue;
            $result['steps'][$id]['controller'] = $controller;

            $action = $stepAttributes->getNamedItem('action')->nodeValue;
            $result['steps'][$id]['action'] = $action;

            /** @var $child DOMNode */
            foreach ($step->childNodes as $child) {
                if ($child->nodeName == 'label') {
                    $result['steps'][$id]['code'] = $child->nodeValue;
                }
            }
        }

        /** @var $step DOMNode */
        foreach ($xpath->query('/install_wizard/filesystem_prerequisites/directory') as $directory) {
            $directoryAttributes = $directory->attributes;
            $alias = $directoryAttributes->getNamedItem('alias')->nodeValue;
            $existence = $directoryAttributes->getNamedItem('existence')->nodeValue == 'true' ? '1' : '0';
            $recursive = $directoryAttributes->getNamedItem('recursive')->nodeValue == 'true' ? '1' : '0';
            if ($directoryAttributes->getNamedItem('writable')->nodeValue == 'true') {
                $result['filesystem_prerequisites']['writables'][$alias]['existence'] = $existence;
                $result['filesystem_prerequisites']['writables'][$alias]['recursive'] = $recursive;
            } else {
                $result['filesystem_prerequisites']['notwritables'][$alias]['existence'] = $existence;
                $result['filesystem_prerequisites']['notwritables'][$alias]['recursive'] = $recursive;
            }
        }

        return $result;
    }
}
