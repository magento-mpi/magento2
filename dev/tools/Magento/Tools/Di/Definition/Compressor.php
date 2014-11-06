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
     * @param array $definitionsList
     * @return mixed
     */
    public function compress(array $definitionsList)
    {
        $signatureList = new Compressor\UniqueList();
        $resultDefinitions = array();
        foreach ($definitionsList as $scope => $definitions) {
            foreach ($definitions as $className => $definition) {
                $resultDefinitions[$scope][$className] = null;
                if ($definition && count($definition)) {
                    $resultDefinitions[$scope][$className] = $signatureList->getNumber($definition);
                }
            }
        }

//        $signatures = $signatureList->asArray();
//        foreach ($signatures as $key => $signature) {
//            $signatures[$key] = $this->_serializer->serialize($signature);
//        }
        return $this->_serializer->serialize($resultDefinitions);
    }
}
