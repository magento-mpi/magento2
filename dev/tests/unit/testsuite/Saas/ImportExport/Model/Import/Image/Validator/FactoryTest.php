<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 * @SuppressWarnings(PHPMD.LongVariable)
 */
class Saas_ImportExport_Model_Import_Image_Validator_FactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_configurationMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_helperMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorBuilderFactoryMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorBuilderMock;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $_validatorMock;

    /**
     * @var Saas_ImportExport_Model_Import_Image_Validator_Factory
     */
    protected $_factory;

    public function setUp()
    {
        $this->_configurationMock = $this->getMock('Saas_ImportExport_Helper_Import_Image_Configuration', array(),
            array(), '', false);
        $this->_helperMock = $this->getMock('Saas_ImportExport_Helper_Data', array(), array(), '', false);
        $this->_helperMock->expects($this->any())->method('__')->with($this->isType('string'))
            ->will($this->returnCallback(function () {
                $args = func_get_args();
                $translated = array_shift($args);
                return vsprintf($translated, $args);
            }));

        $this->_validatorBuilderFactoryMock = $this->getMock('Magento_Validator_BuilderFactory', array('create'),
            array(), '', false);
        $this->_validatorBuilderMock = $this->getMock('Magento_Validator_Builder', array(), array(), '', false);
        $this->_validatorMock = $this->getMock('Magento_Validator_ValidatorInterface', array(), array(), '', false);

        $objectManager = new Magento_Test_Helper_ObjectManager($this);
        $this->_factory = $objectManager->getObject('Saas_ImportExport_Model_Import_Image_Validator_Factory', array(
            'configuration' => $this->_configurationMock,
            'validatorBuilderFactory' => $this->_validatorBuilderFactoryMock,
            'helper' => $this->_helperMock,
        ));
    }

    /**
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @see https://jira.corp.x.com/browse/MAGETWO-10439
     */
    public function testCreateValidator()
    {
        $filenameLimit = 255;
        $allowedExtensions = array('png', 'gif', 'jpg', 'jpe', 'jpeg');
        $sizeLimit = 100000;
        $allowedMimetypes = array('image/png', 'image/gif', 'image/jpeg');
        $widthLimit = 500;
        $heightLimit = 500;

        $messageFilenameWrong = "File name error (only latin a-z, A-Z, 0-9, '-' and '_' symbols are allowed in files"
            . " and folders names) in:";
        $messageFilenameLimit = 'File name is too long:';
        $extensionsString = "'" . implode("', '", array_values($allowedExtensions)) . "'";
        $messageWrongImage = sprintf('Unsupported image format (only %s image file types are allowed) in:',
            $extensionsString);
        $messageFileSizeNotFound = 'File error for:';
        $messageFileSizeTooBig = sprintf('File size is larger than %d bytes in:', $sizeLimit);
        $messageWrongImageSize = sprintf('Image dimensions are larger than %sx%s in:', $widthLimit, $heightLimit);

        $this->_configurationMock->expects($this->once())->method('getImageFilenameLimit')
            ->will($this->returnValue($filenameLimit));
        $this->_configurationMock->expects($this->once())->method('getImageAllowedExtensions')
            ->will($this->returnValue($allowedExtensions));
        $this->_configurationMock->expects($this->once())->method('getImageFileSizeLimit')
            ->will($this->returnValue($sizeLimit));
        $this->_configurationMock->expects($this->once())->method('getImageAllowedMimetypes')
            ->will($this->returnValue($allowedMimetypes));
        $this->_configurationMock->expects($this->once())->method('getImageWidthLimit')
            ->will($this->returnValue($widthLimit));
        $this->_configurationMock->expects($this->once())->method('getImageHeightLimit')
            ->will($this->returnValue($heightLimit));

        $this->_validatorBuilderFactoryMock->expects($this->once())->method('create')
            ->with(array(
                array(
                    array(
                        'alias' => 'FileName',
                        'type' => '',
                        'class' => 'Saas_ImportExport_Model_Import_Image_Validator_FileName',
                        'options' => array(
                            'arguments' => array(
                                array('lengthLimit' => $filenameLimit, 'pattern' => '/^[a-z\d\-_\/\.]+$/i'),
                            ),
                            'methods' => array(
                                array(
                                    'method' => 'setMessages',
                                    'arguments' => array(
                                        array(
                                            Saas_ImportExport_Model_Import_Image_Validator_FileName::NAME_IS_WRONG
                                                => $messageFilenameWrong,
                                            Saas_ImportExport_Model_Import_Image_Validator_FileName::NAME_LENGTH_TOO_BIG
                                                => $messageFilenameLimit,
                                        ),
                                    ),
                                ),
                            ),
                            'breakChainOnFailure' => true,
                        ),
                    ),
                    array(
                        'alias' => 'Extension',
                        'type' => '',
                        'class' => 'Magento_Validator_File_Extension',
                        'options' => array(
                            'arguments' => array($allowedExtensions),
                            'methods' => array(
                                array(
                                    'method' => 'setMessages',
                                    'arguments' => array(
                                        array(
                                            Magento_Validator_File_Extension::FALSE_EXTENSION => $messageWrongImage,
                                            Magento_Validator_File_Extension::NOT_FOUND => $messageWrongImage,
                                        ),
                                    ),
                                ),
                            ),
                            'breakChainOnFailure' => true,
                        ),
                    ),
                    array(
                        'alias' => 'Size',
                        'type' => '',
                        'class' => 'Magento_Validator_File_Size',
                        'options' => array(
                            'arguments' => array($sizeLimit),
                            'methods' => array(
                                array(
                                    'method' => 'setMessages',
                                    'arguments' => array(
                                        array(
                                            Magento_Validator_File_Size::NOT_FOUND => $messageFileSizeNotFound,
                                            Magento_Validator_File_Size::TOO_BIG => $messageFileSizeTooBig,
                                        ),
                                    ),
                                ),
                            ),
                            'breakChainOnFailure' => true,
                        ),
                    ),
                    array(
                        'alias' => 'IsImage',
                        'type' => '',
                        'class' => 'Magento_Validator_File_IsImage',
                        'options' => array(
                            'arguments' => array($allowedMimetypes),
                            'methods' => array(
                                array(
                                    'method' => 'setMessages',
                                    'arguments' => array(
                                        array(
                                            Magento_Validator_File_IsImage::FALSE_TYPE => $messageWrongImage,
                                            Magento_Validator_File_IsImage::NOT_DETECTED => $messageWrongImage,
                                            Magento_Validator_File_IsImage::NOT_READABLE => $messageWrongImage,
                                        ),
                                    ),
                                ),
                            ),
                            'breakChainOnFailure' => true,
                        ),
                    ),
                    array(
                        'alias' => 'ImageSize',
                        'type' => '',
                        'class' => 'Magento_Validator_File_ImageSize',
                        'options' => array(
                            'arguments' => array(
                                array('maxwidth' => $widthLimit, 'maxheight' => $heightLimit),
                            ),
                            'methods' => array(
                                array(
                                    'method' => 'setMessages',
                                    'arguments' => array(
                                        array(
                                            Magento_Validator_File_ImageSize::WIDTH_TOO_BIG => $messageWrongImageSize,
                                            Magento_Validator_File_ImageSize::WIDTH_TOO_SMALL => $messageWrongImageSize,
                                            Magento_Validator_File_ImageSize::HEIGHT_TOO_BIG => $messageWrongImageSize,
                                            Magento_Validator_File_ImageSize::HEIGHT_TOO_SMALL
                                                => $messageWrongImageSize,
                                            Magento_Validator_File_ImageSize::NOT_DETECTED => $messageWrongImage,
                                            Magento_Validator_File_ImageSize::NOT_READABLE => $messageWrongImage,
                                        ),
                                    ),
                                ),
                            ),
                            'breakChainOnFailure' => true,
                        ),
                    ),
                ),
            ))
            ->will($this->returnValue($this->_validatorBuilderMock));

        $this->_validatorBuilderMock->expects($this->once())->method('createValidator')
            ->will($this->returnValue($this->_validatorMock));

        $this->assertEquals($this->_validatorMock, $this->_factory->createValidator());
    }
}
