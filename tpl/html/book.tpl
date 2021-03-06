<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<HTML><HEAD>
    <TITLE>{site_title} - {title_by_author}</TITLE>
    <META NAME="GENERATOR" CONTENT="miniCalOPe">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <LINK REL="stylesheet" TYPE="text/css" HREF="{relurl}tpl/html/style.css">
    {ad_css}
    <LINK REL="alternate" TYPE="application/atom+xml;type=entry;profile=opds-catalog" HREF="{baseurl}index.php?action=bookdetails&amp;book={id}&amp;lang={lang}&amp;pageformat=opds">
    <LINK REL="search" TYPE="application/opensearchdescription+xml" TITLE="{site_title}" HREF="{baseurl}index.php?lang={lang}&amp;prefix=ods&amp;pageformat=html"/>
    <META NAME="viewport" CONTENT="width=device-width; initial-scale=1.0; minimum-scale=0.5; maximum-scale=2.0; user-scalable=1;" />
</HEAD><BODY>

<H1>{site_title} - {title_by_author}</H1>

<TABLE ALIGN='center' BORDER='0' CLASS='list'>
    <TR><TD><A HREF='{relurl}index.php?lang={lang}&amp;pageformat=html'><IMG ALT='home' SRC='{relurl}tpl/icons/home.png'> {start_page}</A></TD></TR>
<!-- BEGIN authorblock -->
    <TR><TD><A HREF='{relurl}index.php?prefix=author_id&amp;query={aid}&amp;lang={lang}&amp;pageformat=html'><IMG ALT='authors' SRC='{relurl}tpl/icons/author.png'>  {authors_page}</A></TD></TR>
<!-- END authorblock -->
<!-- BEGIN serialblock -->
    <TR><TD><A HREF='{relurl}index.php?lang={lang}&amp;pageformat=html&amp;prefix=series_id&amp;query={id}'><IMG ALT='home' SRC='{relurl}tpl/icons/bookseries.png'> {series_page}</A></TD></TR>
<!-- END serialblock -->
<!-- BEGIN flattrblock -->
    <TR><TD><SCRIPT TYPE="text/javascript" ID='flattrbtn'>(function(i){var f,s=document.getElementById(i);f=document.createElement('iframe');f.src='//api.flattr.com/button/view/?uid={flattrID}&button=compact&url='+encodeURIComponent(document.URL);f.title='{title_by_author}';f.tags='eBooks';f.category='text';f.height=20;f.width=110;f.style.borderWidth=0;s.parentNode.insertBefore(f,s);})('flattrbtn');</SCRIPT></TD></TR>
<!-- END flattrblock -->
<!-- BEGIN flattrstaticblock -->
    <TR><TD><a title='Flattr this!' href='https://flattr.com/submit/auto?user_id={flattrID}&amp;url={flattred_url}&amp;title={urlenc_title_by_author}&amp;tags=eBooks&amp;category=text'><img src='{relurl}tpl/icons/flattr-badge.png'></a></TD></TR>
<!-- END flattrstaticblock -->
</TABLE>
<TABLE ALIGN='center' BORDER='0' ID='booktable'>
    <TR><TD STYLE='vertical-align:top;text-align:center;' CLASS='bookcover'>
<!-- BEGIN coverblock -->
        <IMG ALT='cover' SRC='{baseurl}{cover_src}' WIDTH='{cover_width}' STYLE='margin-right:15px;margin-left:5px;'>
<!-- END coverblock -->
<!-- BEGIN fakecoverblock -->
        <DIV CLASS='CoverBlank'><DIV CLASS='innerBorder'><DIV CLASS='BookTitle'>
            {booktitle}
            <DIV CLASS='Author'>{authorname}</DIV>
        </DIV></DIV></DIV>
<!-- END fakecoverblock -->
        </TD><TD>
<!-- BEGIN datablock -->
        <p{dataclass}><b>{data_name}:</b> {data_data}</p>
<!-- END datablock -->
        <div id='bookdesc'>{comment}</div>
        <p><b>{field_download}:</b>
<!-- BEGIN itemblock -->
          <A HREF='{baseurl}index.php?action=getbook&amp;book={id}&amp;format={format}&amp;lang={lang}'>{ftype_human}</A> ({flength_human})&nbsp;
<!-- END itemblock -->
    </TD></TR>
</TABLE>

<DIV CLASS='updated'>{last_update}: {pubdate_human}</DIV>

{adblock}

<!-- BEGIN donationblock -->
<div class='appsig'><a href='{donation_url}' title='Donations'><img src='{donation_img}' height='18'></a></div>
<!-- END donationblock -->

<DIV CLASS='appsig'>{created_by} <A HREF='http://projects.izzysoft.de/trac/minicalope'>miniCalOPe</A></DIV>

</BODY></HTML>
