<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xs="http://www.w3.org/2001/XMLSchema"
    xmlns:mods="http://www.loc.gov/mods/v3" 
    exclude-result-prefixes="xs"
    version="2.0">
    
    <xsl:template match="/">
        <collection xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="vorlage.xsd">
            <xsl:for-each select="//mods:mods">
                <item>
                    <titleBib><xsl:value-of select="mods:titleInfo/mods:title"/></titleBib>
                    <xsl:if test="mods:name">
                        <persons>
                        <xsl:for-each select="mods:name[@type='personal']">
                            <person>
                                <persName><xsl:value-of select="mods:namePart[@type='family']"/><xsl:if test="mods:namePart[@type='given']">, </xsl:if><xsl:value-of select="mods:namePart[@type='given']"/></persName>
                                <role><xsl:value-of select="mods:role/mods:roleTerm"/></role>
                            </person>
                        </xsl:for-each>
                        </persons>
                    </xsl:if>
                    <xsl:if test="mods:originInfo/mods:place/mods:placeTerm">
                        <places>
                            <place><xsl:value-of select="mods:originInfo/mods:place/mods:placeTerm"/></place>
                        </places>
                    </xsl:if>
                    <xsl:if test="mods:originInfo/mods:publisher">
                        <publisher><xsl:value-of select="mods:originInfo/mods:publisher"/></publisher>
                    </xsl:if>
                    <xsl:if test="mods:originInfo/mods:dateIssued">
                        <year><xsl:value-of select="mods:originInfo/mods:dateIssued"/></year>
                    </xsl:if>
                    <xsl:if test="mods:language/mods:languageTerm">
                        <languages>
                            <language><xsl:value-of select="mods:language/mods:languageTerm"/></language>
                        </languages>
                    </xsl:if>
                </item>
            </xsl:for-each>
        </collection>
    </xsl:template>
    
</xsl:stylesheet>