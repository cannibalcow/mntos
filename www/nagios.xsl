<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
<xsl:output method="html" encoding="iso-8859-1" indent="no"/>
<xsl:template match="/">
<html>
<head>

<link rel="stylesheet" type="text/css">
	<xsl:attribute name="href"><xsl:value-of select="$style" />.css</xsl:attribute>
</link>
	
<title>	
	MNTOS - Multi Nagios Tactical Overview System
</title>
	<xsl:choose>
		<xsl:when test="$removemute = '0'">
			<META HTTP-EQUIV="REFRESH">
			<xsl:attribute name="CONTENT"><xsl:value-of select="$refreshtime" /></xsl:attribute>
			</META>

		</xsl:when>
		<xsl:otherwise>
			<meta http-equiv="Refresh">
			<xsl:attribute name="content">5;url=index.php?mute=0&amp;style=<xsl:value-of select="$style" />&amp;network_id=<xsl:value-of select="$network_id" />&amp;critsound=<xsl:value-of select="$critsound" /></xsl:attribute>
			</meta>
		</xsl:otherwise>
	</xsl:choose>
</head>
<body>
<xsl:choose>
<xsl:when test="$critsound = '1'">
<xsl:choose>
	<xsl:when test="$crits &gt; $critacks">
		<xsl:choose>
			<xsl:when test="$mute = '0'">
				<EMBED SRC="critical.wav" LOOP="TRUE" HIDDEN="TRUE" WIDTH="0" HEIGHT="0">
				 <NOEMBED>
					<BGSOUND SRC="critical.wav" LOOP="INFINITE" />
				</NOEMBED>
				</EMBED>
			</xsl:when>
		</xsl:choose>
	</xsl:when>
</xsl:choose>
</xsl:when>
</xsl:choose>

<table id="container">
<tr>
<td>
<table class="bgbord">
<tr>
<td>
<xsl:if test="$removemute = '1'">
<div id="removemute">
Removing mute..
</div>
</xsl:if>
<div id="mute">
<xsl:choose>
	<xsl:when test="$mute = '0'">
	<div id="unmuted">
	<a title="Mute">
	<xsl:attribute name="href"> 
		index.php?mute=1&amp;style=<xsl:value-of select="$style" />&amp;network_id=<xsl:value-of select="$network_id" />&amp;critsound=<xsl:value-of select="$critsound" />
	</xsl:attribute>
	<img src="img/pixel.gif" class="muteimg" />
	</a>
	</div>
	</xsl:when>
	<xsl:otherwise>
	<div id="muted">
	<a title="Unmute">
	<xsl:attribute name="href"> 
		index.php?mute=0&amp;style=<xsl:value-of select="$style" />&amp;network_id=<xsl:value-of select="$network_id" />&amp;critsound=<xsl:value-of select="$critsound" />
	</xsl:attribute>
	<img src="img/pixel.gif" class="muteimg" />
	</a>
	</div>
	</xsl:otherwise>
</xsl:choose>
</div>
<div id="logo"></div>		
<div id="toptext">Multi Nagios Tactical Overview System</div>
<div id="lastcheck">Last check: <xsl:value-of select="nagios/timestamp/stamp"/></div>
</td>
</tr>
<tr>
<td valign="top">
<xsl:for-each select="nagios/networks/network[@id]">
<table class="bord" cellspacing="0" cellpadding="0">
<tr class="title">
	<th colspan="5" class="title">
		<xsl:value-of select="network"/> - 
		<xsl:value-of select="location"/>
	</th>
	<th class="title titleconimages">
	<xsl:variable name="netid">
			<xsl:value-of select="@id"/>
	</xsl:variable>

	<xsl:for-each select="contacts/contact[@contact_id]">
	<a class="conimage">	
	<xsl:attribute name="href"> 
		index.php?style=<xsl:value-of select="$style" />&amp;network_id=<xsl:value-of select="$netid" />&amp;mute=<xsl:value-of select="$mute" />&amp;critsound=<xsl:value-of select="$critsound" />
	</xsl:attribute>
		<img class="conimage" src="img/contact.gif" />  
	</a>
	</xsl:for-each>
	</th>
</tr>

<xsl:choose>
	<xsl:when test="error/error = '1'">
	<tr>
                <td colspan="6" class="errorcell">
                <div class="errorcell">
                        ERROR: Could not retrieve Nagios information for
                        <a target="_blank">
                        <xsl:attribute name="href">
                        <xsl:value-of select="public" />
                        </xsl:attribute>
                        <xsl:value-of select="network"/></a>. If Nagios is up, please check your MNTOS network configuration file.
                </div>
                </td>
	</tr>
	</xsl:when>
	<xsl:otherwise>

<tr class="uplist">
	<td rowspan="2" class="locimage">
		<a target="_blank">
			<xsl:attribute name="href">
			<xsl:value-of select="public" />
			</xsl:attribute>
		<img class="locimage"> 
			<xsl:attribute name="src">
			<xsl:value-of select="icon" />
			</xsl:attribute>
		</img>
		</a>
	</td>

	<td  class="uplist leftpos cell stateup"><xsl:value-of select="hostheader/up"/> up</td>
	<xsl:choose>
		<xsl:when test="hostheader/down &gt; unimportant/acknowledged">
			<td class="uplist cell statedown statedowndif">
			<xsl:value-of select="hostheader/down"/> down
			<xsl:choose>
				<xsl:when test="$mute = '0'">
					<EMBED SRC="down.wav" LOOP="TRUE" HIDDEN="TRUE" WIDTH="0" HEIGHT="0">
					 <NOEMBED>
						<BGSOUND SRC="down.wav" LOOP="INFINITE" />
					</NOEMBED>
					</EMBED>
				</xsl:when>
			</xsl:choose>
			</td>
		</xsl:when>
		<xsl:otherwise>
			<td class="uplist normalpos cell statedown"><xsl:value-of select="hostheader/down"/> down</td>
		</xsl:otherwise>
	</xsl:choose>

	<td class="uplist normalpos cell stateack"><xsl:value-of select="unimportant/acknowledged"/> acknowledged</td>
	<td class="uplist normalpos cell stateunreach"><xsl:value-of select="hostheader/unreachable"/> unreachable</td>
	<td class="uplist rightpos cell statependinghost"><xsl:value-of select="hostheader/pending"/> pending</td>
</tr>
<tr class="downlist">

	<xsl:choose>
		<xsl:when test="service/warning  &gt; 0">
			<td class="downlist leftpos cell statewarning statewarningdif"><xsl:value-of select="service/warning"/> warnings</td>
		</xsl:when>
		<xsl:otherwise>
			<td class="downlist leftpos cell statewarning"><xsl:value-of select="service/warning"/> warnings</td>
		</xsl:otherwise>
	</xsl:choose>

	<xsl:choose>
		<xsl:when test="service/critical &gt; unimportant/critacknowledged">
			<td class="downlist normalpos cell statecrit statecritdif"><xsl:value-of select="service/critical"/> critical</td>
		</xsl:when>
		<xsl:otherwise>
			<td class="downlist normalpos cell statecrit"><xsl:value-of select="service/critical"/> critical</td>
		</xsl:otherwise>
	</xsl:choose>

<td class="downlist normalpos cell stateack"><xsl:value-of select="unimportant/critacknowledged"/> acknowledged</td>

	<xsl:choose>
		<xsl:when test="service/unknown  &gt; 0">
			<td class="downlist normalpos cell stateunknown stateunknowndif"><xsl:value-of select="service/unknown"/> unknown</td>
		</xsl:when>
		<xsl:otherwise>
			<td class="downlist normalpos cell stateunknown"><xsl:value-of select="service/unknown"/> unknown</td>
		</xsl:otherwise>
	</xsl:choose>
<td class="downlist normalpos cell stateok"><xsl:value-of select="service/ok"/> ok</td>
<!--	<td class="downlist rightpos cell statependingservice"><xsl:value-of select="service/pending"/> pending</td> -->
</tr>
	</xsl:otherwise>
</xsl:choose>

</table>
</xsl:for-each>
<div id="crtext"><a href="mailto:danielheldtsmail@gmail.com">MNTOS V 1.0 - Daniel Heldt (c) 2007</a></div>
</td>
<td class="bordcontact">
<table class="bordcontact" cellspacing="0" cellpadding="0">
	<tr>
		<th colspan="0" class="title">
		Contacts for <xsl:value-of select="nagios/networks/network[@id=$network_id]/location"/>
		</th>
	</tr>
	<xsl:for-each select="/nagios/networks/network[@id=$network_id]/contacts/contact">
		<xsl:variable name="conid">
			<xsl:value-of select="@contact_id"/>
		</xsl:variable>

		<xsl:variable name="name">
			<xsl:value-of select="/nagios/contacts/contact[@id=$conid]/name"/>
		</xsl:variable>

		<xsl:variable name="address">
			<xsl:value-of select="/nagios/contacts/contact[@id=$conid]/address"/>
		</xsl:variable>

		<xsl:variable name="zipcode">
			<xsl:value-of select="/nagios/contacts/contact[@id=$conid]/zipcode"/>
		</xsl:variable>
		
		<xsl:variable name="city">
			<xsl:value-of select="/nagios/contacts/contact[@id=$conid]/city"/>
		</xsl:variable>

		<xsl:variable name="country">
			<xsl:value-of select="/nagios/contacts/contact[@id=$conid]/country"/>
		</xsl:variable>

		<xsl:variable name="email">
			<xsl:value-of select="/nagios/contacts/contact[@id=$conid]/email"/>
		</xsl:variable>
	
		<xsl:variable name="workphone">
			<xsl:value-of select="/nagios/contacts/contact[@id=$conid]/workphone"/>
		</xsl:variable>

		<xsl:variable name="privatephone">
			<xsl:value-of select="/nagios/contacts/contact[@id=$conid]/privatephone"/>
		</xsl:variable>

		<xsl:variable name="profession">
			<xsl:value-of select="/nagios/contacts/contact[@id=$conid]/profession"/>
		</xsl:variable>
	<tr>	
		<td class="contitle">
		<xsl:value-of select="$profession"/>
		</td>
	</tr>	
	<tr>
		<td class="coninfo">
		<xsl:value-of select="$name"/><br/>
		<xsl:value-of select="$address"/><br/>
		<xsl:value-of select="$zipcode"/> - <xsl:value-of select="$city"/><br/>
		<xsl:value-of select="$workphone"/><br/>
		<a>
		<xsl:attribute name="href">
				mailto:<xsl:value-of select="$email" />
		</xsl:attribute>
			<xsl:value-of select="$email" />
		</a>
	</td>
	</tr>
	</xsl:for-each>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</body>
</html>
</xsl:template>
</xsl:stylesheet>
