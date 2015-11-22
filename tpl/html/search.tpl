<!DOCTYPE HTML PUBLIC '-//W3C//DTD HTML 4.01 Transitional//EN'>
<HTML><HEAD>
    <TITLE>{site_title}</TITLE>
    <META NAME="GENERATOR" CONTENT="miniCalOPe">
    <META http-equiv="Content-Type" content="text/html; charset=utf-8">
    <LINK REL="stylesheet" TYPE="text/css" HREF="{relurl}tpl/html/style.css">
    {ad_css}
    <!--LINK REL="alternate" TYPE="application/atom+xml;profile=opds-catalog" HREF="{baseurl}index.php?lang={lang}&amp;pageformat=opds"-->
    <LINK REL="search" TYPE="application/opensearchdescription+xml" TITLE="{site_title}" HREF="{baseurl}index.php?lang={lang}&amp;prefix=ods&amp;pageformat=html"/>
    <META NAME="viewport" CONTENT="width=device-width; initial-scale=1.0; minimum-scale=0.5; maximum-scale=2.0; user-scalable=1;" />
</HEAD><BODY>

<H1>{site_title}: {bsearch}</H1>

<FORM NAME='search' ACTION='{baseurl}{form_action}' METHOD='post'>
<TABLE ALIGN='center' BORDER='0'>
    <TR><TD><B>{author_title}:</B></TD><TD><INPUT TYPE='text' NAME='author'></TD>
        <TD ROWSPAN='4'>&nbsp;&nbsp;&nbsp;<B>{tags_title}:</B></TD>
        <TD ROWSPAN='4'><SELECT NAME='tags[]' MULTIPLE='MULTIPLE' SIZE='5'>
<!-- BEGIN tagselblock -->
                <OPTION VALUE='{optval}'>{optname}</OPTION>
<!-- END tagselblock -->
            </SELECT>
        </TD></TR>
    <TR><TD><B>{book_title}:</B></TD><TD><INPUT TYPE='text' NAME='title'></TD></TR>
    <TR><TD><B>{series_title}:</B></TD><TD><INPUT TYPE='text' NAME='series'></TD></TR>
    <TR><TD><B>{desc_title}:</B></TD><TD><INPUT TYPE='text' NAME='desc'></TD></TR>
    <TR><TD COLSPAN='4' ALIGN='center' STYLE='padding:1em;'><INPUT TYPE='submit' NAME='do_search' VALUE='{submit_title}'></TD></TR>
</TABLE>
</FORM>

<DIV CLASS='updated'>{last_update}: {pubdate_human}<BR><SPAN CLASS='count'>({num_allbooks} {allbooks})</SPAN></DIV>

{adblock}

<DIV CLASS='appsig'>{created_by} <A HREF='http://projects.izzysoft.de/trac/minicalope'>miniCalOPe</A></DIV>

</BODY></HTML>