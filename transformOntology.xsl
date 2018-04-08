<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0"
  xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
  xmlns:owl="http://www.w3.org/2002/07/owl#"
  xmlns:rdfs="http://www.w3.org/2000/01/rdf-schema#">
  <xsl:output method="html" indent="yes" encoding="utf-8"/>  
  
  <xsl:template match="/">
  <xsl:text disable-output-escaping='yes'>&lt;!DOCTYPE html&gt;</xsl:text>
  
<html lang ="de">
<head>
<title>LibReTo Ontologie</title>
<meta charset="utf-8"/>
<meta name="viewport" content="width=device-width, initial-scale=1"/>
<link rel="stylesheet" href="assets/css/bootstrap.min.css"/>
<link rel="stylesheet" href="assets/css/affix.css"/>
<link rel="stylesheet" href="assets/css/proprietary.css"/>
<script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script>
<script src="assets/js/proprietary.js"></script>
</head>
<body>
<div class="container" style="min-height:1000px;">

<h1>Ontologie für das Library Reconstruction Tool (LibReTo)</h1>
<p><a href="ontology.rdf">RDF/XML</a>, <a href="ontology.ttl">Turtle</a></p>

<h2>Klassen</h2>
<xsl:apply-templates select="//owl:Class"></xsl:apply-templates>

<h3>Beziehungen zwischen Klassen</h3>
<xsl:apply-templates select="//owl:ObjectProperty"></xsl:apply-templates>

<h3>Beziehungen zwischen Klassen und Werten</h3>
<xsl:apply-templates select="//owl:DatatypeProperty"></xsl:apply-templates>

</div>

<footer class="container-fluid">
<p><a href="index.html" style="color:white">Start</a>&#160;&#160;<a href="http://www.hab.de/de/home/impressum.html" style="color:white" target="_blank">Impressum</a></p>
</footer>

</body>
</html>

  </xsl:template>

<xsl:template match="owl:Class">
<xsl:variable name="url"><xsl:value-of select="self::*/@rdf:about"/></xsl:variable>
<xsl:variable name="label"><xsl:value-of select="self::*/rdfs:label[@xml:lang='de']"/></xsl:variable> 
<xsl:variable name="className"><xsl:call-template name="assignName"><xsl:with-param name="classURL"><xsl:value-of select="$url"/></xsl:with-param></xsl:call-template></xsl:variable>
<h3 id="{$className}"><a href="{$url}"><xsl:value-of select="$className"/></a>&#160;<small>(<xsl:value-of select="$label"></xsl:value-of>)</small></h3>
<xsl:apply-templates select="rdfs:comment"></xsl:apply-templates>
</xsl:template>

<xsl:template match="owl:ObjectProperty">
<xsl:variable name="url"><xsl:value-of select="self::*/@rdf:about"/></xsl:variable>
<xsl:variable name="propertyName"><xsl:call-template name="assignName"><xsl:with-param name="classURL"><xsl:value-of select="$url"/></xsl:with-param></xsl:call-template></xsl:variable>
<h3 id="{$propertyName}"><a href="{$url}"><xsl:value-of select="$propertyName"/></a></h3>
<xsl:apply-templates select="rdfs:comment"></xsl:apply-templates>
<p>Subjekt: <xsl:apply-templates select="rdfs:domain"></xsl:apply-templates></p>
<p>Objekt: <xsl:apply-templates select="rdfs:range"></xsl:apply-templates></p>
</xsl:template>

<xsl:template match="owl:DatatypeProperty">
<xsl:variable name="url"><xsl:value-of select="self::*/@rdf:about"/></xsl:variable>
<xsl:variable name="propertyName"><xsl:call-template name="assignName"><xsl:with-param name="classURL"><xsl:value-of select="$url"/></xsl:with-param></xsl:call-template></xsl:variable>
<h3 id="{$propertyName}"><a href="{$url}"><xsl:value-of select="$propertyName"/></a></h3>
<xsl:apply-templates select="rdfs:comment"></xsl:apply-templates>
<p>Subjekt: <xsl:apply-templates select="rdfs:domain"></xsl:apply-templates></p>
</xsl:template>

<xsl:template match="rdfs:domain">
<xsl:variable name="url"><xsl:value-of select="self::*/@rdf:resource"/></xsl:variable>
<xsl:variable name="className"><xsl:call-template name="assignName"><xsl:with-param name="classURL"><xsl:value-of select="$url"/></xsl:with-param></xsl:call-template></xsl:variable>
<a href="{$url}" title="{$className}"><xsl:value-of select="$className"></xsl:value-of></a>&#160;
</xsl:template>

<xsl:template match="rdfs:range">
<xsl:variable name="url"><xsl:value-of select="self::*/@rdf:resource"/></xsl:variable>
<xsl:variable name="className"><xsl:call-template name="assignName"><xsl:with-param name="classURL"><xsl:value-of select="$url"/></xsl:with-param></xsl:call-template></xsl:variable>
<a href="{$url}" title="{$className}"><xsl:value-of select="$className"></xsl:value-of></a>&#160;
</xsl:template>
  
<xsl:template match="rdfs:comment[@xml:lang='de']">
<p><xsl:value-of select="self::*"></xsl:value-of></p>
</xsl:template>

<xsl:template name="assignName">
<xsl:param name="classURL"/>
<xsl:choose>
<xsl:when test="contains($classURL, 'http://dev.hab.de/bibliotheksrekonstruktion/ontology.html#')"><xsl:value-of select="substring($classURL, 59)"/></xsl:when>
<xsl:when test="contains($classURL, 'http://purl.org/dc/terms/')">dcmt:<xsl:value-of select="substring($classURL, 26)"/></xsl:when>
<xsl:when test="contains($classURL, 'http://xmlns.com/foaf/spec/#')">foaf:<xsl:value-of select="substring($classURL, 29)"/></xsl:when>
<xsl:when test="contains($classURL, 'http://www.geonames.org/ontology#')">gn:<xsl:value-of select="substring($classURL, 34)"/></xsl:when>
<xsl:when test="contains($classURL, 'http://www.w3.org/2003/01/geo/wgs84_pos#')">geo:<xsl:value-of select="substring($classURL, 41)"/></xsl:when>
<xsl:when test="contains($classURL, 'http://dbpedia.org/ontology/')">dbp:<xsl:value-of select="substring($classURL, 29)"/></xsl:when>
<xsl:otherwise>Error</xsl:otherwise>
</xsl:choose>
</xsl:template>
  
</xsl:stylesheet>
