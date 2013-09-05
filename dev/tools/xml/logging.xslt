<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output indent="yes"/>
    <xsl:template match="/">
        <logging xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
                 xsi:noNamespaceSchemaLocation="../../../app/code/Magento/Logging/etc/logging.xsd">
            <xsl:for-each select="logging/*">
                <xsl:if test="local-name() = 'actions'">
                    <xsl:for-each select="./*">
                        <title>
                            <xsl:attribute name="action">
                                <xsl:value-of select='local-name()' />
                            </xsl:attribute>
                            <xsl:attribute name="translate">true</xsl:attribute>
                            <xsl:value-of select="label" />
                        </title>
                    </xsl:for-each>
                </xsl:if>
                <xsl:if test="local-name() != 'actions'">
                    <event>
                        <xsl:attribute name="id">
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
                                <xsl:if test="./additional_data">
                                    <xsl:attribute name="additional_fields">
                                        <xsl:value-of select="for $a in (additional_data/*) return name($a)"
                                                      separator=" " />
                                    </xsl:attribute>
                                </xsl:if>
                                <xsl:if test="./skip_data">
                                    <xsl:attribute name="skip_fields">
                                        <xsl:value-of select="for $a in (skip_data/*) return name($a)"
                                                      separator=" " />
                                    </xsl:attribute>
                                </xsl:if>
                            </expected_model>
                        </xsl:for-each>
                        <xsl:for-each select="./actions/*">
                            <handle>
                                <xsl:attribute name="name">
                                    <xsl:value-of select="local-name()" />
                                </xsl:attribute>
                                <xsl:if test="./action">
                                    <xsl:attribute name="action">
                                        <xsl:value-of select="action" />
                                    </xsl:attribute>
                                </xsl:if>
                                <xsl:if test="./skip_on_back">
                                    <xsl:attribute name="skip_on_back">
                                        <xsl:value-of select="for $a in (skip_on_back/*) return name($a)"
                                                      separator=" " />
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
                                        <xsl:if test="./additional_data">
                                            <xsl:attribute name="additional_fields">
                                                <xsl:for-each select="./additional_data/*">
                                                    <xsl:value-of select="local-name()" />
                                                    <xsl:text> </xsl:text>
                                                </xsl:for-each>
                                            </xsl:attribute>
                                        </xsl:if>
                                        <xsl:if test="./skip_data">
                                            <xsl:attribute name="skip_fields">
                                                <xsl:for-each select="./skip_data/*">
                                                    <xsl:value-of select="local-name()" />
                                                    <xsl:text> </xsl:text>
                                                </xsl:for-each>
                                            </xsl:attribute>
                                        </xsl:if>
                                    </expected_model>
                                </xsl:for-each>
                            </handle>
                        </xsl:for-each>
                    </event>
                </xsl:if>
            </xsl:for-each>
        </logging>
    </xsl:template>
</xsl:stylesheet>
