<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<HTML><HEAD>
    <TITLE>{site_title} - {title_by_author}</TITLE>
    <META NAME="GENERATOR" CONTENT="miniCalOPe">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <LINK REL="stylesheet" TYPE="text/css" HREF="{relurl}tpl/html/style.css">
    <LINK REL="alternate" TYPE="application/atom+xml;type=entry;profile=opds-catalog" HREF="{baseurl}?action=bookdetails&amp;book={id}&amp;lang={lang}&amp;pageformat=opds">
    <LINK REL="search" TYPE="application/opensearchdescription+xml" TITLE="{site_title}" HREF="{baseurl}?lang={lang}&amp;prefix=ods&amp;pageformat=html"/>
    <META NAME="viewport" CONTENT="width=device-width; initial-scale=1.0; minimum-scale=0.5; maximum-scale=2.0; user-scalable=1;" />
</HEAD><BODY>

<H1>{site_title} - {title_by_author}</H1>

<TABLE ALIGN='center' BORDER='0' CLASS='list'>
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html'><IMG ALT='home' SRC='{relurl}tpl/icons/home.png'> {start_page}</A></TD></TR>
<!-- BEGIN authorblock -->
    <TR><TD><A HREF='{relurl}?prefix=author_id&amp;query={aid}&amp;lang={lang}&amp;pageformat=html'><IMG ALT='authors' SRC='{relurl}tpl/icons/author.png'>  {authors_page}</A></TD></TR>
<!-- END authorblock -->
<!-- BEGIN serialblock -->
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;prefix=series_id&amp;query={id}'><IMG ALT='home' SRC='{relurl}tpl/icons/bookseries.png'> {series_page}</A></TD></TR>
<!-- END serialblock -->
</TABLE>
<TABLE ALIGN='center' BORDER='0'>
    <TR><TD STYLE='vertical-align:top;'>
<!-- BEGIN coverblock -->
        <IMG ALIGN='center' ALT='cover' SRC='{baseurl}{cover_src}' WIDTH='{cover_width}' STYLE='margin-right:15px;margin-left:5px;'>
<!-- END coverblock -->
<!-- BEGIN fakecoverblock -->
        <DIV CLASS='CoverBlank'><DIV CLASS='innerBorder'><DIV CLASS='BookTitle'>
            {booktitle}
            <DIV CLASS='Author'>{authorname}</DIV>
        </DIV></DIV></DIV>
<!-- END fakecoverblock -->
        </TD><TD>
<!-- BEGIN datablock -->
        <p><b>{data_name}:</b> {data_data}</p>
<!-- END datablock -->
        <p><b>{field_comment}:</b><br/>{comment}</p>
        <p><b>{field_download}:</b>
<!-- BEGIN itemblock -->
          <A HREF='{baseurl}?action=getbook&amp;book={id}&amp;format={format}&amp;lang={lang}'>{ftype_human}</A> ({flength_human})&nbsp;
<!-- END itemblock -->
    </TD></TR>
</TABLE>

<!-- BEGIN adblock -->
<DIV CLASS='offers'>
<script type="text/javascript"><!--
 amazon_ad_tag = "{amazonID}";
 amazon_ad_width = "468";
 amazon_ad_height = "60";
 amazon_ad_logo = "hide";
 amazon_ad_link_target = "new";
 amazon_color_border = "{amazon_bordercolor}";
 amazon_color_logo = "{amazon_logocolor}";
 amazon_ad_include = "{booktitle_urlenc};{authorname_urlenc};{booktags_urlenc}";
//--></script>
<script type="text/javascript" src="http://www.assoc-amazon.de/s/ads.js"></script>
</DIV>
<!-- END adblock -->

<DIV CLASS='updated'>{last_update}: {pubdate_human}</DIV>

<DIV CLASS='appsig'>{created_by} <A HREF='http://projects.izzysoft.de/trac/minicalope'>miniCalOPe</A></DIV>

</BODY></HTML>
