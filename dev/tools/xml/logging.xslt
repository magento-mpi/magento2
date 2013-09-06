<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output indent="yes"/>
    <xsl:template match="/">
        <logging xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                 xsi:noNamespaceSchemaLocation="../../../Magento/Logging/etc/logging.xsd">
            <xsl:for-each select="logging/*">
                <xsl:if test="local-name() = 'actions'">
                    <xsl:for-each select="./*">
                        <action>
                            <xsl:attribute name="id">
                                <xsl:value-of select='local-name()' />
                            </xsl:attribute>
                            <label>
                                <xsl:attribute name="translate">true</xsl:attribute>
                                <xsl:value-of select="label" />
                            </label>
                        </action>
                    </xsl:for-each>
                </xsl:if>
                <xsl:if test="local-name() != 'actions'">
                    <log>
                        <xsl:attribute name="name">
                            <xsl:value-of select='local-name()' />
                        </xsl:attribute>
                        <label>
                            <xsl:attribute name="translate">true</xsl:attribute>
                            <xsl:value-of select='label' />
                        </label>
                        <xsl:for-each select="./expected_models/*">
                            <expected_model>
                                <xsl:attribute name="class">
                                    <xsl:value-of select='local-name()' />
                                </xsl:attribute>
                                <xsl:for-each select="./additional_data/*">
                                    <additional_field >
                                        <xsl:value-of select='local-name()' />
                                    </additional_field >
                                </xsl:for-each>
                                <xsl:for-each select="./skip_data/*">
                                    <skip_field >
                                        <xsl:value-of select='local-name()' />
                                    </skip_field >
                                </xsl:for-each>
                            </expected_model>
                        </xsl:for-each>
                        <xsl:for-each select="./actions/*">
                            <event>
                                <xsl:attribute name="controller_action">
                                    <xsl:value-of select="local-name()" />
                                </xsl:attribute>
                                <xsl:if test="./action">
                                    <xsl:attribute name="action_alias">
                                        <xsl:value-of select="action" />
                                    </xsl:attribute>
                                </xsl:if>
                                <xsl:if test="./post_dispatch">
                                    <xsl:attribute name="post_dispatch">
                                        <xsl:value-of select="post_dispatch" />
                                    </xsl:attribute>
                                </xsl:if>
                                <xsl:if test="expected_models[contains(@extends, 'merge')]">
                                    <xsl:attribute name="extends_expected_models">true</xsl:attribute>
                                </xsl:if>
                                <xsl:for-each select="./expected_models/*">
                                    <expected_model>
                                        <xsl:attribute name="class">
                                            <xsl:value-of select="local-name()" />
                                        </xsl:attribute>
                                        <xsl:for-each select="./additional_data/*">
                                            <additional_field >
                                                <xsl:value-of select='local-name()' />
                                            </additional_field >
                                        </xsl:for-each>
                                        <xsl:for-each select="./skip_data/*">
                                            <skip_field >
                                                <xsl:value-of select='local-name()' />
                                            </skip_field >
                                        </xsl:for-each>
                                    </expected_model>
                                </xsl:for-each>
                                <xsl:for-each select="./skip_on_back/*">
                                    <skip_on_back >
                                        <xsl:value-of select='local-name()' />
                                    </skip_on_back>
                                </xsl:for-each>
                            </event>
                        </xsl:for-each>
                    </log>
                </xsl:if>
            </xsl:for-each>
        </logging>
    </xsl:template>
</xsl:stylesheet>
