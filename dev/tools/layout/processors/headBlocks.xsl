<xsl:stylesheet version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:php="http://php.net/xsl"
    xsl:extension-element-prefixes="php"
    exclude-result-prefixes="xsl php"
    >

    <!-- Copy nodes -->
    <xsl:template match="node()|@*">
        <xsl:copy>
            <xsl:apply-templates select="node()|@*" />
        </xsl:copy>
    </xsl:template>

    <xsl:template match="action[@method='addJs' or @method='addCss']">
        <block>
            <xsl:attribute name="type">
                <xsl:choose>
                    <xsl:when test="@method = 'addJs' ">Mage_Page_Block_Html_Head_Script</xsl:when>
                    <xsl:when test="@method = 'addCss'">Mage_Page_Block_Html_Head_Css</xsl:when>
                </xsl:choose>
            </xsl:attribute>
            <xsl:attribute name="name">
                <xsl:value-of select="php:function('strtolower', php:function('trim', php:function('preg_replace', '/[^a-z]+/i', '-', string(./*[1])), '-'))" />
            </xsl:attribute>
            <xsl:apply-templates select="@ifconfig" />
            <arguments>
                <file>
                    <xsl:value-of select="*[1]" />
                </file>
                <xsl:if test="count(*[position() &gt; 1])">
                    <properties>
                        <xsl:if test="*[2]"><attributes><xsl:value-of select="*[2]" /></attributes></xsl:if>
                        <xsl:if test="*[3]"><ie_condition><xsl:value-of select="*[3]" /></ie_condition></xsl:if>
                        <xsl:if test="*[4]"><flag_name><xsl:value-of select="*[4]" /></flag_name></xsl:if>
                    </properties>
                </xsl:if>
            </arguments>
        </block>
    </xsl:template>

    <xsl:template match="//reference[action[@method='removeItem']]">
        <xsl:copy>
            <xsl:apply-templates select="node()|@*" />
        </xsl:copy>
        <xsl:for-each select="action[@method='removeItem']">
            <remove name="{php:function('strtolower', php:function('trim', php:function('preg_replace', '/[^a-z]+/i', '-', string(*[2])), '-'))}" />
        </xsl:for-each>
      </xsl:template>

    <!-- Delete remove item call -->
    <xsl:template match="action[@method='removeItem']">
    </xsl:template>

</xsl:stylesheet>