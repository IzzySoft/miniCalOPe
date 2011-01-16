<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:dcterms="http://purl.org/dc/terms/"
      xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/"
      xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/">

  <title>Bücher mit dem Tag {tag_name}</title>
  <subtitle>miniCalOPe.</subtitle>
  <id>{baseurl}?default_prefix=tag_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset={offset}&amp;lang={lang}</id>
  <updated>{pubdate}</updated>

  <author>
    <name>{owner}</name>
    <uri>{homepage}</uri>
    <email>{email}</email>
  </author>

  <link rel="self" title="Diese Seite"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?default_prefix=tag_id&amp;sort_order={sortorder}&amp;query={aid}&amp;lang={lang}"/>
<!-- BEGIN prevblock -->
  <link rel="first" title="Erste Seite" type="application/atom+xml" href="{baseurl}?default_prefix=tag_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset=0"/>
  <link rel="previous" title="Vorige Seite" type="application/atom+xml" href="{baseurl}?default_prefix=tag_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset={poffset}"/>
<!-- END prevblock -->
<!-- BEGIN nextblock -->
  <link rel="next" title="Nächste Seite" type="application/atom+xml" href="{baseurl}?default_prefix=tag_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset={noffset}"/>
  <link rel="last" title="Letzte Seite" type="application/atom+xml" href="{baseurl}?default_prefix=tag_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset={loffset}"/>
<!-- END nextblock -->
  <link rel="http://opds-spec.org/sort/start" title="Gehe an den Start"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?lang={lang}"/>
  <link rel="http://opds-spec.org/sort/new" title="Nach Datum sortieren"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}/?default_prefix=tag_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}"/>
  <link rel="alternate" title="Nach Titel sortieren"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}/?default_prefix=tag_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}"/>
  <link rel="alternate" title="Nach Autor sortieren"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?default_prefix=tag_id&amp;sort_order=author&amp;query={aid}&amp;lang={lang}"/>
  <link rel="up" title="Zurück zu allen Tags"
        type="application/atom+xml;profile=opds-catalog"
        href="{relurl}?default_prefix=tags&amp;lang={lang}"/>

  <opensearch:totalResults>{total}</opensearch:totalResults>
  <opensearch:startIndex>{start}</opensearch:startIndex>
  <opensearch:itemsPerPage>{per_page}</opensearch:itemsPerPage>

  <entry>
    <title>Gehe an den Start</title>
    <id>{baseurl}?lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/home.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Zurück zu allen Tags</title>
    <id>{baseurl}?default_prefix=authors&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=tags&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/tags.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Alphabetisch sortieren</title>
    <id>baseurl}?default_prefix=tag_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=tag_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/alpha.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Nach Autor sortieren</title>
    <id>{baseurl}?default_prefix=tag_id&amp;sort_order=author&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=tag_id&amp;sort_order=author&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/authors.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Nach Datum sortieren</title>
    <id>{baseurl}?default_prefix=tag_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{relurl}?default_prefix=tag_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/date.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

<!-- BEGIN itemblock -->
  <entry>
    <title>{title} by {author}</title>
    <id>{baseurl}{bid}.opds</id>
    <content type="text">ISBN: {isbn}</content>
    <link type="application/atom+xml;type=entry;profile=opds-catalog" href="{baseurl}?action=bookdetails&amp;book={bid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/book.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>
<!-- END itemblock -->

</feed>