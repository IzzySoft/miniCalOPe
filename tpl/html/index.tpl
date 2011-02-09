<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<HTML><HEAD>
    <TITLE>{site_title}</TITLE>
    <META NAME="GENERATOR" CONTENT="miniCalOPe">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <LINK REL="stylesheet" TYPE="text/css" HREF="{relurl}tpl/html/style.css">
    <LINK REL="alternate" TYPE="application/atom+xml;profile=opds-catalog" HREF="{baseurl}?lang={lang}&amp;pageformat=opds">
</HEAD><BODY>

<H1>{site_title}</H1>

<TABLE ALIGN='center' BORDER='0' CLASS='list'>
    <TR><TD><A HREF='{relurl}?prefix=authors&amp;lang={lang}&amp;pageformat=html'><IMG ALT='author' SRC='{relurl}tpl/icons/authors.png'> {author_list}</A></TD></TR>
    <TR><TD><A HREF='{relurl}?prefix=titles&amp;lang={lang}&amp;pageformat=html'><IMG ALT='books' SRC='{relurl}tpl/icons/bookcase.png'> {title_list}</A></TD></TR>
    <TR><TD><A HREF='{relurl}?prefix=tags&amp;lang={lang}&amp;pageformat=html'><IMG ALT='topics' SRC='{relurl}tpl/icons/tags.png'> {tags_list}</A></TD></TR>
    <TR><TD><A HREF='{relurl}?prefix=series&amp;lang={lang}&amp;pageformat=html'><IMG ALT='series' SRC='{relurl}tpl/icons/bookseries.png'> {series_list}</A></TD></TR>
</TABLE>

<DIV CLASS='updated'>{last_update}: {pubdate_human}<BR><SPAN CLASS='count'>({num_allbooks} {allbooks})</SPAN></DIV>

<DIV CLASS='appsig'>{created_by} <A HREF='http://projects.izzysoft.de/trac/minicalope'>miniCalOPe</A></DIV>

</BODY></HTML>