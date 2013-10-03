<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
  xmlns:dcterms="http://purl.org/dc/terms/"
  xml:base="{baseurl}">
  <id>{baseurl}index.php?lang={lang}</id>
  <link rel="self"
        href="{baseurl}index.php?lang={lang}"
        type="application/atom+xml;profile=opds-catalog"/>
  <link rel="start"
        href="{baseurl}index.php?lang={lang}"
        type="application/atom+xml;profile=opds-catalog"/>
  <link rel="search"
        type="application/opensearchdescription+xml"
        href="{baseurl}?lang={lang}&amp;prefix=ods"/>
  <link rel="search"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?lang={lang}&amp;pageformat=opds&amp;prefix=searchresults&amp;q={searchTerms}"/>
  <title>{site_title}</title>
  <updated>{pubdate}</updated>
  <author>
    <name>{owner}</name>
    <uri>{homepage}</uri>
    <email>{email}</email>
  </author>

  <entry>
    <title>{author_list}</title>
    <id>{baseurl}?prefix=authors&amp;lang={lang}</id>
    <content type="text">{author_list}</content>
    <link type="application/atom+xml;profile=opds-catalog" href="{relurl}?prefix=authors&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/authors.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{title_list}</title>
    <id>{baseurl}?prefix=titles&amp;lang={lang}</id>
    <content type="text">{title_list}</content>
    <link type="application/atom+xml;profile=opds-catalog" href="{relurl}?prefix=titles&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/bookcase.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{tags_list}</title>
    <id>{baseurl}?prefix=subjects&amp;lang={lang}</id>
    <content type="text">{tags_list}</content>
    <link type="application/atom+xml;profile=opds-catalog" href="{relurl}?prefix=tags&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/tags.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{series_list}</title>
    <id>{baseurl}?prefix=series&amp;lang={lang}</id>
    <content type="text">{series_list}</content>
    <link type="application/atom+xml;profile=opds-catalog" href="{relurl}?prefix=series&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/bookseries.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

</feed>