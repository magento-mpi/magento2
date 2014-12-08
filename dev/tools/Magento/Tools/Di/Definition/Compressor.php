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
    /**
     * @var Serializer\SerializerInterface
     */
    protected $_serializer;

    /**
     * @param Serializer\SerializerInterface $serializer
     */
    public function __construct(Serializer\SerializerInterface $serializer)
    {
        $this->_serializer = $serializer;
    }

    /**
     * Compress array definitions
     *
     * @param array $definitions
     * @return mixed
     */
    public function compress(array $definitions)
    {
        $signatureList = new Compressor\UniqueList();
        $resultDefinitions = [];
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
        return $this->_serializer->serialize([$signatures, $resultDefinitions]);
    }
}
