<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
namespace Magento\Tools\Di\Definition;


class Compressor
{
    protected $_serializer;

    public function __construct(Serializer $serializer)
    {
        $this->_serializer = $serializer;
    }

    public function compress($definitions)
    {
        $signatureList = new UniqueList();
        $resultDefinitions = array();
        foreach ($definitions as $className => $definition) {
            $resultDefinitions[$className] = null;
            if ($definition && count($definition)) {
                $resultDefinitions[$className] = $signatureList->getNumber($definition);
            }
        }

        $signatures = $signatureList->asArray();
        foreach ($signatures as $key => $signature) {
            $signatures[$key] = $this->_serializer->serialize($signature);
        }
        return $this->_serializer->serialize(array($signatures, $resultDefinitions));
    }
}
