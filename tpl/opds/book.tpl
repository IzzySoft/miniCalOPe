<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:dcterms="http://purl.org/dc/terms/"
      xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/">

  <id>{baseurl}{id}.opds</id>
  <title>{title} von {author}</title>
  <subtitle>miniCalOPe.</subtitle>
  <updated>{pubdate}</updated>

  <author>
    <name>{owner}</name>
    <uri>{homepage}</uri>
    <email>{email}</email>
  </author>

  <link rel="self" title="Diese Seite"
        type="application/atom+xml;type=entry;profile=opds-catalog"
        href="{baseurl}?action=bookdetails&amp;book={id}&amp;lang={lang}"/>
  <link rel="http://opds-spec.org/sort/start" title="Gehe an den Start"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?lang={lang}"/>
  <link rel="up" title="Zurück zu allen Autoren"
        type="application/atom+xml;profile=opds-catalog"
        href="{relurl}?default_prefix=authors&amp;lang={lang}"/>

  <entry>
    <title>Gehe an den Start</title>
    <id>{baseurl}?lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/home.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>


  <entry>
    <title>{title}</title>
    <id>urn:calibre:{id}</id>
    <updated>{pubdate}</updated>
    <published>{pubdate}</published>
    <!--rights>Gemeinfrei in den USA.</rights-->
    <author>
      <name>{author}</name>
    </author>
    <content type="xhtml">
      <div xmlns="http://www.w3.org/1999/xhtml">
        <p><b>Titel:</b> {title}</p>
        <p><b>Autor:</b> {author}</p>
        <p><b>Tags:</b> {tags}</p>
        <p><b>Reihe:</b> {series}</p>
        <p><b>Herausgeber:</b> {publisher}</p>
        <p><b>ISBN:</b> {isbn}</p>
        <p><b>Kommentar:</b><br/>{comment}</p>
      </div>
    </content>

<!-- BEGIN coverblock -->
    <link type="image/{cover_type}" href="{baseurl}{cover_src}" rel="http://opds-spec.org/cover" />
    <link type="image/{cover_type}" href="{baseurl}{cover_src}" rel="http://opds-spec.org/image" />
<!-- END coverblock -->
    <link type="image/png" href="{relurl}tpl/icons/book.png" rel="http://opds-spec.org/image/thumbnail"/>
<!-- BEGIN itemblock -->
    <link type="application/{ftype}" rel="http://opds-spec.org/acquisition" title="{ftitle}" length="{flength}" href="{baseurl}?action=getbook&amp;book={id}&amp;format={format}&amp;lang={lang}"/>
<!-- END itemblock -->
  </entry>

</feed>