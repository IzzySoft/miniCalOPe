<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<HTML><HEAD>
    <TITLE>{site_title} - {title} von {author}</TITLE>
    <META NAME="GENERATOR" CONTENT="miniCalOPe">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <LINK REL="stylesheet" TYPE="text/css" HREF="{relurl}tpl/html/style.css">
    <LINK REL="alternate" TYPE="application/atom+xml;type=entry;profile=opds-catalog" HREF="{baseurl}?action=bookdetails&amp;book={id}&amp;lang={lang}&amp;pageformat=opds">
</HEAD><BODY>

<H1>{site_title} - {title} von {author}</H1>

<TABLE ALIGN='center' BORDER='0' CLASS='list'>
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html'><IMG ALT='home' SRC='{relurl}tpl/icons/home.png'> Startseite</A></TD></TR>
</TABLE>
<TABLE ALIGN='center' BORDER='0'>
    <TR><TD>
        <p><b>Titel:</b> {title}</p>
        <p><b>Autor:</b> {author}</p>
        <p><b>Tags:</b> {tags}</p>
        <p><b>Reihe:</b> {series}</p>
        <p><b>Herausgeber:</b> {publisher}</p>
        <p><b>ISBN:</b> {isbn}</p>
        <p><b>Kommentar:</b><br/>{comment}</p>
        <p><b>Download:</b>
<!-- BEGIN itemblock -->
          <A HREF='{baseurl}?action=getbook&amp;book={id}&amp;format={format}&amp;lang={lang}'>{ftype_human}</A> ({flength_human})&nbsp;
<!-- END itemblock -->
    </TD></TR>
<!-- BEGIN coverblock -->
    <TR><TD ALIGN='center'>
        <IMG ALIGN='center' ALT='cover' SRC='{baseurl}{cover_src}' WIDTH='{cover_width}'>
    </TD></TR>
<!-- END coverblock -->
</TABLE>

<DIV CLASS='updated'>Letzte Aktualisierung: {pubdate_human}</DIV>

<DIV CLASS='appsig'>Created by <A HREF='http://projects.izzysoft.de/trac/minicalope'>miniCalOPe</A></DIV>

</BODY></HTML>
