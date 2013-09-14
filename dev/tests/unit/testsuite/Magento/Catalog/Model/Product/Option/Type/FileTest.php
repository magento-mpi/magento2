<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Product_Option_Type_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider createWritableDirDataProvider
     * @param boolean $isWritable
     * @param boolean $throwException
     */
    public function testCreateWritableDir($isWritable, $throwException)
    {
        $helper = new Magento_TestFramework_Helper_ObjectManager($this);
        $filesystemMock = $this->getMock('Magento\Filesystem', array(), array(), '', false);
        $filesystemMock->expects($this->once())
            ->method('isWritable')
            ->will($this->returnValue($isWritable));
        if (!$isWritable) {
            $filesystemMock->expects($this->once())
                ->method('createDirectory')
                ->will($throwException
                    ? $this->throwException(new \Magento\Filesystem\FilesystemException)
                    : $this->returnValue(null)
                );
        } else {
            $filesystemMock->expects($this->never())->method('createDirectory');
        }

        if ($throwException) {
            $this->setExpectedException('Magento\Core\Exception');
        }

        $parameters = array('filesystem' => $filesystemMock);
        $model = $helper->getObject('Magento\Catalog\Model\Product\Option\Type\File', $parameters);
        $method = new ReflectionMethod('Magento\Catalog\Model\Product\Option\Type\File', '_createWritableDir');
        $method->setAccessible(true);
        $method->invoke($model, 'dummy/path');
    }

    /**
     * @see self::testCreateWritableDir()
     * @return array
     */
    public function createWritableDirDataProvider()
    {
        return array(
            array(true, false),
            array(false, false),
            array(false, true),
        );
    }
}
