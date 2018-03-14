<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:mods="http://www.loc.gov/mods/v3" version="1.0">
    
    <xsl:template match="/">
        <collection xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:noNamespaceSchemaLocation="http://dev.hab.de/auktionskataloge/uploadXML.xsd">
            <xsl:for-each select="//mods:mods">
                <item>
                    <titleBib><xsl:value-of select="mods:titleInfo/mods:title"/></titleBib>
                    <xsl:if test="mods:name">
                        <persons>
                            <xsl:for-each select="mods:name[@type='personal']">
                                <person>
                                    <persName><xsl:value-of select="mods:namePart[@type='family']"/><xsl:if test="mods:namePart[@type='given']">, </xsl:if><xsl:value-of select="mods:namePart[@type='given']"/></persName>
                                    <xsl:if test="mods:role/mods:roleTerm = 'aut'">
                                        <role>author</role>
                                    </xsl:if>
                                    <xsl:if test="mods:role/mods:roleTerm = 'edt'">
                                        <role>contributor</role>
                                    </xsl:if>
                                    <xsl:if test="mods:role/mods:roleTerm = 'edr'">
                                        <role>contributor</role>
                                    </xsl:if>                                    
                                    <xsl:if test="mods:role/mods:roleTerm = 'trl'">
                                        <role>translator</role>
                                    </xsl:if>
                                    <xsl:if test="mods:role/mods:roleTerm = 'wac'">
                                        <role>commentator</role>
                                    </xsl:if>                                         
                                    <xsl:if test="mods:role/mods:roleTerm != 'aut' and mods:role/mods:roleTerm != 'edr' and mods:role/mods:roleTerm != 'edt' and mods:role/mods:roleTerm != 'trl' and mods:role/mods:roleTerm != 'wac'">
                                        <role><xsl:value-of select="mods:role/mods:roleTerm"/></role>
                                    </xsl:if>
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
                    <xsl:choose>
                        <xsl:when test="mods:originInfo/mods:dateIssued">
                            <year><xsl:value-of select="mods:originInfo/mods:dateIssued"/></year>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:if test="mods:originInfo/mods:copyrightDate">
                                <year><xsl:value-of select="mods:originInfo/mods:copyrightDate"/></year>
                            </xsl:if>
                        </xsl:otherwise>
                    </xsl:choose>
                    <xsl:if test="mods:language/mods:languageTerm">
                        <languages>
                            <language><xsl:value-of select="mods:language/mods:languageTerm"/></language>
                        </languages>
                    </xsl:if>
                    <xsl:if test="mods:subject">
                        <subjects>
                            <xsl:for-each select="mods:subject"><subject><xsl:value-of select="mods:topic"/></subject></xsl:for-each>
                        </subjects>
                    </xsl:if>
                    <xsl:if test="mods:location/mods:url">
                        <digitalCopy><xsl:value-of select="mods:location/mods:url"/></digitalCopy>
                    </xsl:if>
                    <xsl:if test="mods:note">
                        <comment><xsl:value-of select="mods:note/text()" /></comment>
                    </xsl:if>                          
                </item>
            </xsl:for-each>
        </collection>
    </xsl:template>
    
</xsl:stylesheet>