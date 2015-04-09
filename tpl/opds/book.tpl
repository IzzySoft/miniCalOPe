<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:dcterms="http://purl.org/dc/terms/"
      xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/">

  <id>{baseurl}{id}.opds</id>
  <title>{title_by_author}</title>
  <subtitle>miniCalOPe.</subtitle>
  <updated>{pubdate}</updated>

  <author>
    <name>{owner}</name>
    <uri>{homepage}</uri>
    <email>{email}</email>
  </author>

  <link rel="self" title="{this_page}"
        type="application/atom+xml;type=entry;profile=opds-catalog"
        href="{baseurl}index.php?action=bookdetails&amp;book={id}&amp;lang={lang}"/>
  <link rel="http://opds-spec.org/sort/start" title="{start_page}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}index.php?lang={lang}"/>
  <link rel="up" title="{back_to_authors}"
        type="application/atom+xml;profile=opds-catalog"
        href="{relurl}index.php?prefix=authors&amp;lang={lang}"/>
  <link rel="search"
        type="application/opensearchdescription+xml"
        href="{baseurl}index.php?lang={lang}&amp;prefix=ods"/>
  <link rel="search"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}index.php?lang={lang}&amp;pageformat=opds&amp;prefix=searchresults&amp;q={searchTerms}"/>
<!-- BEGIN flattrblock -->
  <link rel="payment"
        type="text/html"
        href="https://flattr.com/submit/auto?url={flattred_url}&amp;user_id={flattrID}"/>
<!-- END flattrblock -->

  <entry>
    <title>{start_page}</title>
    <id>{baseurl}index.php?lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}index.php?lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/home.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

<!-- BEGIN authorblock -->
  <entry>
    <title>{authors_page}</title>
    <id>{baseurl}index.php?prefix=author_id&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}index.php?prefix=author_id&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/author.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>
<!-- END authorblock -->

<!-- BEGIN serialblock -->
  <entry>
    <title>{series_page}</title>
    <id>{baseurl}index.php?lang={lang}&amp;prefix=series_id&amp;query={id}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}index.php?lang={lang}&amp;prefix=series_id&amp;query={id}"/>
    <link type="image/png" href="{relurl}tpl/icons/bookseries.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>
<!-- END serialblock -->

  <entry>
    <title>{booktitle}</title>
    <id>urn:calibre:{id}</id>
    <updated>{pubdate}</updated>
    <published>{pubdate}</published>
    <!--rights>Gemeinfrei in den USA.</rights-->
    <author>
      <name>{authorname}</name>
    </author>
    <content type="xhtml">
      <div xmlns="http://www.w3.org/1999/xhtml">
<!-- BEGIN datablock -->
        <p><b>{data_name}:</b> {data_data}</p>
<!-- END datablock -->
        <div><b>{field_comment}:</b><br/>{comment}</div>
      </div>
    </content>

<!-- BEGIN coverblock -->
    <link type="image/{cover_type}" href="{baseurl}{cover_src}" rel="http://opds-spec.org/cover" />
    <link type="image/{cover_type}" href="{baseurl}{cover_src}" rel="http://opds-spec.org/image" />
<!-- END coverblock -->
<!-- BEGIN fakecoverblock -->

<!-- END fakecoverblock -->
    <link type="image/png" href="{relurl}tpl/icons/book.png" rel="http://opds-spec.org/image/thumbnail"/>
<!-- BEGIN itemblock -->
    <link type="{ftype}" rel="http://opds-spec.org/acquisition" title="{ftitle}" length="{flength}" href="{baseurl}index.php?action=getbook&amp;book={id}&amp;format={format}&amp;lang={lang}"/>
<!-- END itemblock -->
  </entry>

<!-- BEGIN adblock -->

<!-- END adblock -->

</feed>