<?php
/**
 * The list of all expected soap fault XMLs.
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
return array(
    'expectedResultArrayDataDetails' =>
    '<?xml version="1.0" encoding="utf-8" ?>
    <env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
        <env:Body>
            <env:Fault>
                <env:Code>
                    <env:Value>env:Sender</env:Value>
                </env:Code>
                <env:Reason>
                    <env:Text xml:lang="cn">Fault reason</env:Text>
                </env:Reason>
                <env:Detail>
                    <key1>value1</key1>
                    <key2>value2</key2>
                </env:Detail>
            </env:Fault>
        </env:Body>
    </env:Envelope>',
    'expectedResultEmptyArrayDetails' =>
    '<?xml version="1.0" encoding="utf-8" ?>
    <env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
        <env:Body>
            <env:Fault>
                <env:Code>
                    <env:Value>env:Sender</env:Value>
                </env:Code>
                <env:Reason>
                    <env:Text xml:lang="en">Fault reason</env:Text>
                </env:Reason>
                <env:Detail></env:Detail>
            </env:Fault>
        </env:Body>
    </env:Envelope>',
    'expectedResultObjectDetails' =>
    '<?xml version="1.0" encoding="utf-8" ?>
    <env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
        <env:Body>
            <env:Fault>
                <env:Code>
                    <env:Value>env:Sender</env:Value>
                </env:Code>
                <env:Reason>
                    <env:Text xml:lang="en">Fault reason</env:Text>
                </env:Reason>
            </env:Fault>
        </env:Body>
    </env:Envelope>',
    'expectedResultStringDetails' =>
    '<?xml version = "1.0" encoding = "utf-8" ?>
    <env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
        <env:Body>
            <env:Fault>
                <env:Code>
                    <env:Value>env:Sender</env:Value>
                </env:Code>
                <env:Reason>
                    <env:Text xml:lang="en">Fault reason</env:Text>
                </env:Reason>
                <env:Detail>String details</env:Detail>
            </env:Fault>
        </env:Body>
    </env:Envelope>',
    'expectedResultIndexArrayDetails' =>
    '<?xml version = "1.0" encoding = "utf-8" ?>
    <env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
        <env:Body>
            <env:Fault>
                <env:Code>
                    <env:Value>env:Sender</env:Value>
                </env:Code>
                <env:Reason>
                    <env:Text xml:lang="en">Fault reason</env:Text>
                </env:Reason>
                <env:Detail></env:Detail>
            </env:Fault>
        </env:Body>
    </env:Envelope>',
    'expectedResultComplexDataDetails' =>
    '<?xml version = "1.0" encoding = "utf-8" ?>
    <env:Envelope xmlns:env="http://www.w3.org/2003/05/soap-envelope">
        <env:Body>
            <env:Fault>
                <env:Code>
                    <env:Value>env:Sender</env:Value>
                </env:Code>
                <env:Reason>
                    <env:Text xml:lang="en">Fault reason</env:Text>
                </env:Reason>
                <env:Detail>
                    <key>
                        <sub_key>value</sub_key>
                    </key>
                </env:Detail>
            </env:Fault>
        </env:Body>
    </env:Envelope>'
);
