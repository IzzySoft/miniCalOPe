<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<HTML><HEAD>
    <TITLE>{site_title} - {title_by_author}</TITLE>
    <META NAME="GENERATOR" CONTENT="miniCalOPe">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <LINK REL="stylesheet" TYPE="text/css" HREF="{relurl}tpl/html/style.css">
    <LINK REL="alternate" TYPE="application/atom+xml;type=entry;profile=opds-catalog" HREF="{baseurl}?action=bookdetails&amp;book={id}&amp;lang={lang}&amp;pageformat=opds">
</HEAD><BODY>

<H1>{site_title} - {title_by_author}</H1>

<TABLE ALIGN='center' BORDER='0' CLASS='list'>
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html'><IMG ALT='home' SRC='{relurl}tpl/icons/home.png'> {start_page}</A></TD></TR>
</TABLE>
<TABLE ALIGN='center' BORDER='0'>
    <TR><TD>
<!-- BEGIN coverblock -->
        <IMG ALIGN='center' ALT='cover' SRC='{baseurl}{cover_src}' WIDTH='{cover_width}' STYLE='margin-right:15px;margin-left:5px;'>
<!-- END coverblock -->
        </TD><TD>
        <p><b>{field_title}:</b> {title}</p>
        <p><b>{field_author}:</b> {author}</p>
        <p><b>{field_tags}:</b> {tags}</p>
        <p><b>{field_serie}:</b> {series}</p>
        <p><b>{field_publisher}:</b> {publisher}</p>
        <p><b>ISBN:</b> {isbn}</p>
        <p><b>{field_comment}:</b><br/>{comment}</p>
        <p><b>{field_download}:</b>
<!-- BEGIN itemblock -->
          <A HREF='{baseurl}?action=getbook&amp;book={id}&amp;format={format}&amp;lang={lang}'>{ftype_human}</A> ({flength_human})&nbsp;
<!-- END itemblock -->
    </TD></TR>
</TABLE>

<DIV CLASS='updated'>{last_update}: {pubdate_human}</DIV>

<DIV CLASS='appsig'>{created_by} <A HREF='http://projects.izzysoft.de/trac/minicalope'>miniCalOPe</A></DIV>

</BODY></HTML>
