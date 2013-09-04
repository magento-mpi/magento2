<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="2.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:output indent="yes" />

    <xsl:template name="refactor-translate">
        <xsl:param name="node" />
        <xsl:param name="translate">false</xsl:param>
        <xsl:variable name="to_translate" select="@translate" />
        <xsl:copy>
            <xsl:if test="$translate = 'true'">
                <xsl:attribute name="translate">true</xsl:attribute>
            </xsl:if>
            <xsl:copy-of select="@*[name()!='translate']" />
            <xsl:if test="text() != '' ">
                <xsl:value-of select="text()[normalize-space()]" />
            </xsl:if>
            <xsl:for-each select="$node/*">
                <xsl:choose>
                    <xsl:when test="contains($to_translate, local-name())">
                        <xsl:call-template name="refactor-translate">
                            <xsl:with-param name="node" select="." />
                            <xsl:with-param name="translate" select="'true'" />
                        </xsl:call-template>
                    </xsl:when>
                    <xsl:otherwise>
                        <xsl:call-template name="refactor-translate">
                            <xsl:with-param name="node" select="." />
                        </xsl:call-template>
                    </xsl:otherwise>
                </xsl:choose>
            </xsl:for-each>
        </xsl:copy>
    </xsl:template>

    <xsl:template match="./.">
        <xsl:comment>
/**
* {license_notice}
*
* @copyright   {copyright}
* @license     {license_link}
*/
</xsl:comment>
        <xsl:call-template name="refactor-translate">
            <xsl:with-param name="node" select="." />
        </xsl:call-template>
    </xsl:template>
</xsl:stylesheet>