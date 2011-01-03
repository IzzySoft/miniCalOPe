<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:dcterms="http://purl.org/dc/terms/"
      xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/"
      xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/">

  <id>{baseurl}?default_prefix=author_id&amp;sort_order=downloads&amp;query=35&amp;lang={lang}</id>
  <title>Bücher von {author_name}</title>
  <updated>{pubdate}</updated>
  <subtitle>miniCalOPe.</subtitle>

  <author>
    <name>{owner}</name>
    <uri>{homepage}</uri>
    <email>{email}</email>
  </author>

  <link rel="self" title="Diese Seite"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?default_prefix=author_id&amp;sort_order=downloads&amp;query={aid}&amp;lang={lang}"/>
  <!--link rel="next" title="Nächste Seite" type="application/atom+xml" href="/ebooks/search.opds/?default_prefix=author_id&amp;sort_order=downloads&amp;query=35&amp;start_index=26"/-->
  <!-- previous, first, last -->
  <link rel="http://opds-spec.org/sort/start" title="Gehe an den Start"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?lang={lang}"/>
  <link rel="http://opds-spec.org/sort/new" title="Nach Datum sortieren"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}/?default_prefix=author_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}"/>
  <link rel="alternate" title="Nach Titel sortieren"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}/?default_prefix=author_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}"/>
  <link rel="up" title="Zurück zu allen Autoren"
        type="application/atom+xml;profile=opds-catalog"
        href="{relurl}?default_prefix=authors&amp;lang={lang}"/>

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
    <title>Zurück zu allen Autoren</title>
    <id>{baseurl}?default_prefix=authors&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=authors&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/authors.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Alphabetisch sortieren</title>
    <id>baseurl}?default_prefix=author_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" rel="alternate" href="{baseurl}?default_prefix=author_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/alpha.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Nach Datum sortieren</title>
    <id>{baseurl}?default_prefix=author_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" rel="alternate" href="{baseurl}?default_prefix=author_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/date.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Wikipedia</title>
    <id>{wikibase}{wikiauthor}</id>
    <content type="text"></content>
    <link type="application/xhtml+xml" href="{wikibase}{wikiauthor}"/>
    <link type="image/png" href="{relurl}tpl/icons/world.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

<!-- BEGIN itemblock -->
  <entry>
    <title>{title}</title>
    <id>{baseurl}{bid}.opds</id>
    <content type="text">ISBN: {isbn}</content>
    <link type="application/atom+xml;type=entry;profile=opds-catalog" href="{baseurl}?action=bookdetails&amp;book={bid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/book.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>
<!-- END itemblock -->

</feed>