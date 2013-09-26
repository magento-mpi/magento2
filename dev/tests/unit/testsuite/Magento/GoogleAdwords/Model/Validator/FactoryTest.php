<?php

namespace Magento\GoogleAdwords\Model\Validator;

/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configurationMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_builderFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_builderMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorMock;

    /**
     * @var \Magento\GoogleAdwords\Model\Validator\Factory
     */
    protected $_factory;

    protected function setUp()
    {
        $this->_builderFactoryMock = $this->getMock('Magento\Validator\BuilderFactory', array('create'),
            array(), '', false);
        $this->_builderMock = $this->getMock('Magento\Validator\Builder', array(), array(), '', false);
        $this->_validatorMock = $this->getMock('Magento\Validator\ValidatorInterface', array(), array(), '', false);

        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->_factory = $objectManager->getObject('Magento\GoogleAdwords\Model\Validator\Factory', array(
            'validatorBuilderFactory' => $this->_builderFactoryMock,
        ));
    }

    public function testCreateColorValidator()
    {
        $currentColor = 'fff';
        $message = sprintf('Conversion Color value is not valid "%s". Please set hexadecimal 6-digit value.',
            $currentColor);

        $this->_builderFactoryMock->expects($this->once())->method('create')
            ->with(array(
                'constraints' => array(
                    array(
                        'alias' => 'Regex',
                        'type' => '',
                        'class' => 'Magento\Validator\Regex',
                        'options' => array(
                            'arguments' => array('/^[0-9a-f]{6}$/i'),
                            'methods' => array(
                                array(
                                    'method' => 'setMessages',
                                    'arguments' => array(
                                        array(
                                            \Magento\Validator\Regex::NOT_MATCH => $message,
                                            \Magento\Validator\Regex::INVALID => $message,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ))
            ->will($this->returnValue($this->_builderMock));

        $this->_builderMock->expects($this->once())->method('createValidator')
            ->will($this->returnValue($this->_validatorMock));

        $this->assertEquals($this->_validatorMock, $this->_factory->createColorValidator($currentColor));
    }

    public function testCreateConversionIdValidator()
    {
        $conversionId = '123';
        $message = sprintf('Conversion Id value is not valid "%s". Conversion Id should be an integer.', $conversionId);

        $this->_builderFactoryMock->expects($this->once())->method('create')
            ->with(array(
                'constraints' => array(
                    array(
                        'alias' => 'Int',
                        'type' => '',
                        'class' => 'Magento\Validator\Int',
                        'options' => array(
                            'methods' => array(
                                array(
                                    'method' => 'setMessages',
                                    'arguments' => array(
                                        array(
                                            \Magento\Validator\Int::NOT_INT => $message,
                                            \Magento\Validator\Int::INVALID => $message,
                                        ),
                                    ),
                                ),
                            ),
                        ),
                    ),
                ),
            ))
            ->will($this->returnValue($this->_builderMock));

        $this->_builderMock->expects($this->once())->method('createValidator')
            ->will($this->returnValue($this->_validatorMock));

        $this->assertEquals($this->_validatorMock, $this->_factory->createConversionIdValidator($conversionId));
    }
}
