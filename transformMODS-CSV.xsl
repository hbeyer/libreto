<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" xmlns:mods="http://www.loc.gov/mods/v3" version="1.0">
    
    <xsl:output method="text" indent="no" encoding="windows-1250" omit-xml-declaration="yes" />
    
    <xsl:template match="/">sep=,
id,pageCat,imageCat,numberCat,itemInVolume,titleCat,titleBib,titleNormalized,author1,author2,author3,author4,contributor1,contributor2,contributor3,contributor4,place1,place2,publisher,year,format,histSubject,subjects,genres,mediaType,languages,systemManifestation,idManifestation,institutionOriginal,shelfmarkOriginal,provenanceAttribute,digitalCopyOriginal,targetOPAC,searchID,titleWork,systemWork,idWork,bound,comment,digitalCopy
            <xsl:for-each select="//mods:mods">
                <xsl:variable name="author1"><xsl:value-of select="mods:name[1]/mods:namePart[@type='family']"/><xsl:if test="mods:name[1]/mods:namePart[@type='given']">, </xsl:if><xsl:value-of select="mods:name[1]/mods:namePart[@type='given']"/></xsl:variable>
                <xsl:variable name="author2"></xsl:variable>
                <xsl:variable name="author3"></xsl:variable>
                <xsl:variable name="author4"></xsl:variable>              
                <xsl:variable name="contributor1"></xsl:variable>
                <xsl:variable name="contributor2"></xsl:variable>   
                <xsl:variable name="contributor3"></xsl:variable>   
                <xsl:variable name="contributor4"></xsl:variable>
                <xsl:variable name="subjects"><xsl:for-each select="mods:subject"><xsl:if test="substring(mods:topic, 1, 3) != 'gvk'"><xsl:value-of select="mods:topic"/>;</xsl:if></xsl:for-each></xsl:variable>
                <xsl:variable name="year">
                    <xsl:choose>
                        <xsl:when test="mods:originInfo/mods:dateIssued">
                            <xsl:value-of select="mods:originInfo/mods:dateIssued"/>
                        </xsl:when>
                        <xsl:otherwise>
                            <xsl:if test="mods:originInfo/mods:copyrightDate">
                                <xsl:value-of select="mods:originInfo/mods:copyrightDate"/>
                            </xsl:if>
                        </xsl:otherwise>
                    </xsl:choose>
                </xsl:variable>
                <xsl:variable name="count"><xsl:number count="mods:mods"/></xsl:variable>
                <xsl:variable name="id"><xsl:value-of select="$count - 1"/></xsl:variable>                
<xsl:value-of select="$id"/>,,,,,,"<xsl:value-of select="mods:titleInfo/mods:title"/>",,"<xsl:value-of select="$author1"/>",,,,,,,,"<xsl:value-of select="mods:originInfo/mods:place/mods:placeTerm[1]"/>","<xsl:value-of select="mods:originInfo/mods:place/mods:placeTerm[2]"/>","<xsl:value-of select="mods:originInfo/mods:publisher"/>","<xsl:value-of select="$year"/>",,,"<xsl:value-of select="$subjects"/>",,,"<xsl:value-of select="mods:language/mods:languageTerm"/>",,,,,,,,,,,,,,"<xsl:value-of select="mods:note/text()" />","<xsl:value-of select="mods:location/mods:url/text()"/>"
</xsl:for-each>
</xsl:template>
    
</xsl:stylesheet>