<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<HTML><HEAD>
    <TITLE>{site_title} - {tags_list}</TITLE>
    <META NAME="GENERATOR" CONTENT="miniCalOPe">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <LINK REL="stylesheet" TYPE="text/css" HREF="{relurl}tpl/html/style.css">
    <LINK REL="alternate" TYPE="application/atom+xml;profile=opds-catalog" HREF="{baseurl}?prefix=tags&amp;lang={lang}&amp;sort_order={sortorder}&amp;offset={offset}&amp;pageformat=opds">
    <LINK REL="search" TYPE="application/opensearchdescription+xml" TITLE="{site_title}" HREF="{baseurl}?lang={lang}&amp;prefix=ods&amp;pageformat=html"/>
    <META NAME="viewport" CONTENT="width=device-width; initial-scale=1.0; minimum-scale=0.5; maximum-scale=2.0; user-scalable=1;" />
</HEAD><BODY>

<H1>{site_title} - {tags_list}</H1>

<TABLE ALIGN='center' BORDER='0' CLASS='list'>
    <TR><TD COLSPAN='2'><A HREF='{relurl}?lang={lang}&amp;pageformat=html'><IMG ALT='home' SRC='{relurl}tpl/icons/home.png'> {start_page}</A></TD></TR>
    <TR><TD COLSPAN='2'><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;prefix=tags&amp;sort_order=title'><IMG ALT='alpha' SRC='{relurl}tpl/icons/alpha.png'> {sort_alpha}</A></TD></TR>
    <TR><TD COLSPAN='2'><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;prefix=tags&amp;sort_order=books'><IMG ALT='numsort' SRC='{relurl}tpl/icons/bookcase.png'> {sort_bookcount}</A></TD></TR>
<!-- BEGIN itemblock -->
    <TR><TD COLSPAN='2'><A HREF='{relurl}?lang={lang}&amp;pageformat=html&amp;prefix=tag_id&amp;query={id}'><IMG ALT='tag' SRC='{relurl}tpl/icons/tag.png'> {name}</A>
            <SPAN CLASS='count'>({num_books} {books})</SPAN></TD></TR>
<!-- END itemblock -->
    <TR><TD COLSPAN='2'>&nbsp;</TD></TR>
    <TR><TD>
<!-- BEGIN prevblock -->
        {link1_open}<IMG ALT='{first_page}' SRC='{rel_url}tpl/icons/{icon1}'>{link_close}
        {link2_open}<IMG ALT='{prev_page}' SRC='{rel_url}tpl/icons/{icon2}'>{link_close}
<!-- END prevblock -->
        </TD><TD ALIGN='right'>
<!-- BEGIN nextblock -->
        {link1_open}<IMG ALT='{next_page}' SRC='{rel_url}tpl/icons/{icon1}'>{link_close}
        {link2_open}<IMG ALT='{last_page}' SRC='{rel_url}tpl/icons/{icon2}'>{link_close}
<!-- END nextblock -->
        </TD></TR>
</TABLE>

<DIV CLASS='updated'>{last_update}: {pubdate_human}<BR><SPAN CLASS='count'>({num_allbooks} {allbooks})</SPAN></DIV>

<DIV CLASS='appsig'>{created_by} <A HREF='http://projects.izzysoft.de/trac/minicalope'>miniCalOPe</A></DIV>

</BODY></HTML>
