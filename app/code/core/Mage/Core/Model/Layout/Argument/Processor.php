<?php

class Mage_Core_Model_Layout_Argument_Processor
{
    /**
     * @var Mage_Core_Model_Layout_Argument_ProcessorConfig
     */
    protected $_config;

    /**
     * @var Mage_Core_Model_Config
     */
    protected $_objectFactory;

    /**
     * @param array $args
     * @throws InvalidArgumentException
     */
    public function __construct(array $args = array())
    {
        if (!isset($args['processorConfig']) || !isset($args['objectFactory'])) {
            throw new InvalidArgumentException('Not all required parameters were passed');
        }

        $this->_config = $args['processorConfig'];
        if (false === ($this->_config instanceof Mage_Core_Model_Layout_Argument_ProcessorConfig)) {
            throw new InvalidArgumentException('Passed wrong instance of processor config object');
        }

        $this->_objectFactory = $args['objectFactory'];
        if (false === ($this->_objectFactory instanceof Mage_Core_Model_Config)) {
            throw new InvalidArgumentException('Passed wrong instance of object factory');
        }
    }

    /**
     * Process given arguments, prepare arguments of custom type.
     * @param array $arguments
     * @throws InvalidArgumentException
     * @return array
     */
    public function process(array $arguments)
    {
        $processedArguments = array();
        foreach ($arguments as $argumentKey => $argumentValue) {
            if (isset($argumentValue['value'])) {
                $processedArguments[$argumentKey] = $argumentValue['value'];

                if (!isset($argumentValue['type'])) {
                    continue;
                }

                $handlerClassName = $this->_config->getArgumentHandlerByType($argumentValue['type']);

                /** @var $handler Mage_Core_Model_Layout_Argument_Processor_TypeInterface */
                $handler = $this->_objectFactory->getModelInstance($handlerClassName, array(
                    'objectFactory' => $this->_objectFactory
                ));

                if (false === ($handler instanceof Mage_Core_Model_Layout_Argument_Processor_TypeInterface)) {
                    throw new InvalidArgumentException($argumentValue['type']
                        . ' type handler should implement Mage_Core_Model_Layout_Argument_Processor_TypeInterface');
                }
                $processedArguments[$argumentKey] = $handler->process($argumentValue['value']);
            }
        }
        return $processedArguments;
    }
}
