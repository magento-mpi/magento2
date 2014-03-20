<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
namespace Magento\GoogleAdwords\Model\Validator;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\Validator\Int;
use Magento\Validator\Regex;

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
    protected $_vbFactoryMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_vbMock;

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
        $this->_vbFactoryMock = $this->getMock(
            'Magento\Validator\UniversalFactory',
            array('create'),
            array(),
            '',
            false
        );
        $this->_vbMock = $this->getMock('Magento\Validator\Builder', array(), array(), '', false);
        $this->_validatorMock = $this->getMock('Magento\Validator\ValidatorInterface', array(), array(), '', false);

        $objectManager = new ObjectManager($this);
        $this->_factory = $objectManager->getObject(
            'Magento\GoogleAdwords\Model\Validator\Factory',
            array('validatorBuilderFactory' => $this->_vbFactoryMock)
        );
    }

    public function testCreateColorValidator()
    {
        $currentColor = 'fff';
        $message = sprintf(
            'Conversion Color value is not valid "%s". Please set hexadecimal 6-digit value.',
            $currentColor
        );

        $this->_vbFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Validator\Builder',
            array(
                'constraints' => array(
                    array(
                        'alias' => 'Regex',
                        'type' => '',
                        'class' => 'Magento\Validator\Regex',
                        'options' => array(
                            'arguments' => array('pattern' => '/^[0-9a-f]{6}$/i'),
                            'methods' => array(
                                array(
                                    'method' => 'setMessages',
                                    'arguments' => array(
                                        array(Regex::NOT_MATCH => $message, Regex::INVALID => $message)
                                    )
                                )
                            )
                        )
                    )
                )
            )
        )->will(
            $this->returnValue($this->_vbMock)
        );

        $this->_vbMock->expects(
            $this->once()
        )->method(
            'createValidator'
        )->will(
            $this->returnValue($this->_validatorMock)
        );

        $this->assertEquals($this->_validatorMock, $this->_factory->createColorValidator($currentColor));
    }

    public function testCreateConversionIdValidator()
    {
        $conversionId = '123';
        $message = sprintf(
            'Conversion Id value is not valid "%s". Conversion Id should be an integer.',
            $conversionId
        );

        $this->_vbFactoryMock->expects(
            $this->once()
        )->method(
            'create'
        )->with(
            'Magento\Validator\Builder',
            array(
                'constraints' => array(
                    array(
                        'alias' => 'Int',
                        'type' => '',
                        'class' => 'Magento\Validator\Int',
                        'options' => array(
                            'methods' => array(
                                array(
                                    'method' => 'setMessages',
                                    'arguments' => array(array(Int::NOT_INT => $message, Int::INVALID => $message))
                                )
                            )
                        )
                    )
                )
            )
        )->will(
            $this->returnValue($this->_vbMock)
        );

        $this->_vbMock->expects(
            $this->once()
        )->method(
            'createValidator'
        )->will(
            $this->returnValue($this->_validatorMock)
        );

        $this->assertEquals($this->_validatorMock, $this->_factory->createConversionIdValidator($conversionId));
    }
}
