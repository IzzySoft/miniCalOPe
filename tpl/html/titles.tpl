<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<HTML><HEAD>
    <TITLE>{site_title} - Alle Titel</TITLE>
    <META NAME="GENERATOR" CONTENT="miniCalOPe">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <LINK REL="stylesheet" TYPE="text/css" HREF="{relurl}tpl/html/style.css">
    <LINK REL="alternate" TYPE="application/atom+xml;profile=opds-catalog" HREF="{baseurl}?default_prefix=titels&amp;sort_order=downloads&amp;lang={lang}&amp;pageformat=opds">
</HEAD><BODY>

<H1>{site_title} - Alle Titel</H1>

<TABLE ALIGN='center' BORDER='0' CLASS='list'>
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html'><IMG ALT='home' SRC='{relurl}tpl/icons/home.png'> Startseite</A></TD></TR>
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;default_prefix=titles&amp;sort_order=title'><IMG ALT='alpha' SRC='{relurl}tpl/icons/alpha.png'> Alphabetisch sortieren</A></TD></TR>
<!-- BEGIN itemblock -->
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;action=bookdetails&amp;book={bid}'><IMG ALT='book' SRC='{relurl}tpl/icons/book.png'> {title}</A></TD></TR>
<!-- END itemblock -->
</TABLE>

<DIV CLASS='updated'>Letzte Aktualisierung: {pubdate_human}<BR>({num_allbooks} {allbooks})</DIV>

<DIV CLASS='appsig'>Created by <A HREF='http://projects.izzysoft.de/trac/minicalope'>miniCalOPe</A></DIV>

</BODY></HTML>
