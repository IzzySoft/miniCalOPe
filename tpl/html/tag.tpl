<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<HTML><HEAD>
    <TITLE>{site_title} - Bücher mit dem Tag {tag_name}</TITLE>
    <META NAME="GENERATOR" CONTENT="miniCalOPe">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <LINK REL="stylesheet" TYPE="text/css" HREF="{relurl}tpl/html/style.css">
    <LINK REL="alternate" TYPE="application/atom+xml;profile=opds-catalog" HREF="{baseurl}?default_prefix=author_id&amp;query=35&amp;lang={lang}&amp;pageformat=opds">
</HEAD><BODY>

<H1>{site_title} - Bücher mit dem Tag {tag_name}</H1>

<TABLE ALIGN='center' BORDER='0' CLASS='list'>
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html'><IMG ALT='home' SRC='{relurl}tpl/icons/home.png'> Startseite</A></TD></TR>
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;default_prefix=tags'><IMG ALT='tags' SRC='{relurl}tpl/icons/tags.png'> Zurück zu allen Tags</A></TD></TR>
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;default_prefix=tag_id&amp;sort_order=title&amp;query={aid}'><IMG ALT='alpha' SRC='{relurl}tpl/icons/alpha.png'> Alphabetisch sortieren</A></TD></TR>
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;default_prefix=tag_id&amp;sort_order=author&amp;query={aid}'><IMG ALT='authors' SRC='{relurl}tpl/icons/authors.png'> Nach Autor sortieren</A></TD></TR>
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;default_prefix=tag_id&amp;sort_order=release_date&amp;query={aid}'><IMG ALT='date' SRC='{relurl}tpl/icons/date.png'> Nach Datum sortieren</A></TD></TR>
<!-- BEGIN itemblock -->
    <TR><TD><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;action=bookdetails&amp;book={bid}'><IMG ALT='book' SRC='{relurl}tpl/icons/book.png'> {title} by {author}</A></TD></TR>
<!-- END itemblock -->
</TABLE>

<DIV CLASS='updated'>Letzte Aktualisierung: {pubdate_human}</DIV>

<DIV CLASS='appsig'>Created by <A HREF='http://projects.izzysoft.de/trac/minicalope'>miniCalOPe</A></DIV>

</BODY></HTML>
