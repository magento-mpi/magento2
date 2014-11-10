<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'options without model attribute' => array(
        '<?xml version="1.0"?><page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <body>
                <block class="Magento\Test\Block" name="test.block">
                    <arguments>
                        <argument name="argumentName" xsi:type="options" />
                    </arguments>
                </block>
            </body>
        </page>',
        array("Element 'argument': The attribute 'model' is required but missing.")),
    'url without path attribute' => array(
        '<?xml version="1.0"?><page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <body>
                <block class="Magento\Test\Block" name="test.block">
                    <arguments>
                        <argument name="argumentName" xsi:type="url" />
                    </arguments>
                </block>
            </body>
        </page>',
        array("Element 'argument': The attribute 'path' is required but missing.")),
    'url without param name' => array(
        '<?xml version="1.0"?><page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <body>
                <block class="Magento\Test\Block" name="test.block">
                    <arguments>
                        <argument name="argumentName" xsi:type="url" path="module/controller/action">
                            <param />
                        </argument>
                    </arguments>
                </block>
            </body>
        </page>',
        array("Element 'param': The attribute 'name' is required but missing.")),
    'url with forbidden param attribute' => array(
        '<?xml version="1.0"?><page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <body>
            <block class="Magento\Test\Block" name="test.block">
                    <arguments>
                        <argument name="argumentName" xsi:type="url" path="module/controller/action">
                            <param name="paramName" forbidden="forbidden"/>
                        </argument>
                    </arguments>
                </block>
            </body>
        </page>',
        array("Element 'param', attribute 'forbidden': The attribute 'forbidden' is not allowed.")),
    'url with forbidden param sub-element' => array(
        '<?xml version="1.0"?><page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <body>
                <block class="Magento\Test\Block" name="test.block">
                    <arguments>
                        <argument name="argumentName" xsi:type="url" path="module/controller/action">
                            <param name="paramName"><forbidden /></param>
                        </argument>
                    </arguments>
                </block>
            </body>
        </page>',
        array("Element 'forbidden': This element is not expected.")),
    'helper without helper attribute' => array(
        '<?xml version="1.0"?><page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <body>
                <block class="Magento\Test\Block" name="test.block">
                    <arguments>
                        <argument name="argumentName" xsi:type="helper" />
                    </arguments>
                </block>
            </body>
        </page>',
        array("Element 'argument': The attribute 'helper' is required but missing.")),
    'helper without param name' => array(
        '<?xml version="1.0"?><page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <body>
                <block class="Magento\Test\Block" name="test.block">
                    <arguments>
                        <argument name="argumentName" xsi:type="helper"
                            helper="Magento\Core\Model\Layout\Argument\Handler\TestHelper::testMethod">
                            <param />
                        </argument>
                    </arguments>
                </block>
            </body>
        </page>',
        array("Element 'param': The attribute 'name' is required but missing.")),
    'helper with forbidden param attribute' => array(
        '<?xml version="1.0"?><page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <body>
                <block class="Magento\Test\Block" name="test.block">
                    <arguments>
                        <argument name="argumentName" xsi:type="helper"
                            helper="Magento\Core\Model\Layout\Argument\Handler\TestHelper::testMethod">
                            <param name="paramName" forbidden="forbidden"/>
                        </argument>
                    </arguments>
                </block>
            </body>
        </page>',
        array("Element 'param', attribute 'forbidden': The attribute 'forbidden' is not allowed.")),
    'helper with forbidden param sub-element' => array(
        '<?xml version="1.0"?><page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
            <body>
                <block class="Magento\Test\Block" name="test.block">
                    <arguments>
                        <argument name="argumentName" xsi:type="helper"
                            helper="Magento\Core\Model\Layout\Argument\Handler\TestHelper::testMethod">
                            <param name="paramName"><forbidden /></param>
                        </argument>
                    </arguments>
                </block>
            </body>
        </page>',
        array("Element 'forbidden': This element is not expected.")),
    'action with doubled arguments' => array(
            '<?xml version="1.0"?><page xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
                <body>
                    <block class="Magento\Test\Block" name="test.block">
                        <action method="testAction">
                            <argument name="string" xsi:type="string">string1</argument>
                            <argument name="string" xsi:type="string">string2</argument>
                        </action>
                    </block>
                </body>
            </page>',
        array(
            "Element 'argument': Duplicate key-sequence ['string'] in key identity-constraint 'actionArgumentName'."
        )),
);
