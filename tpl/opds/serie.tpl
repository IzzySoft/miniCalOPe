<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:dcterms="http://purl.org/dc/terms/"
      xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/"
      xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/">

  <title>{books_in_serie}</title>
  <subtitle>miniCalOPe.</subtitle>
  <id>{baseurl}index.php?prefix=series_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset={offset}&amp;lang={lang}</id>
  <updated>{pubdate}</updated>

  <author>
    <name>{owner}</name>
    <uri>{homepage}</uri>
    <email>{email}</email>
  </author>

  <link rel="self" title="{this_page}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}index.php?prefix=series_id&amp;sort_order={sortorder}&amp;query={aid}&amp;lang={lang}"/>
<!-- BEGIN prevblock -->
  <link rel="first" title="{first_page}" type="application/atom+xml" href="{baseurl}index.php?prefix=series_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset=0"/>
  <link rel="previous" title="{prev_page}" type="application/atom+xml" href="{baseurl}index.php?prefix=series_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset={poffset}"/>
<!-- END prevblock -->
<!-- BEGIN nextblock -->
  <link rel="next" title="{next_page}" type="application/atom+xml" href="{baseurl}index.php?prefix=series_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset={noffset}"/>
  <link rel="last" title="{last_page}" type="application/atom+xml" href="{baseurl}index.php?prefix=series_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset={loffset}"/>
<!-- END nextblock -->
  <link rel="http://opds-spec.org/sort/start" title="{start_page}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}index.php?lang={lang}"/>
  <link rel="http://opds-spec.org/sort/new" title="{sort_index}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}/?prefix=series_id&amp;sort_order=index&amp;query={aid}&amp;lang={lang}"/>
  <link rel="alternate" title="{sort_alpha}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}/?prefix=series_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}"/>
  <link rel="alternate" title="{sort_author}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}index.php?prefix=series_id&amp;sort_order=author&amp;query={aid}&amp;lang={lang}"/>
  <link rel="up" title="{back_to_series}"
        type="application/atom+xml;profile=opds-catalog"
        href="{relurl}index.php?prefix=series&amp;lang={lang}"/>
  <link rel="search"
        type="application/opensearchdescription+xml"
        href="{baseurl}index.php?lang={lang}&amp;prefix=ods"/>
  <link rel="search"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}index.php?lang={lang}&amp;pageformat=opds&amp;prefix=searchresults&amp;q={searchTerms}"/>

  <opensearch:totalResults>{total}</opensearch:totalResults>
  <opensearch:startIndex>{start}</opensearch:startIndex>
  <opensearch:itemsPerPage>{per_page}</opensearch:itemsPerPage>

  <entry>
    <title>{start_page}</title>
    <id>{baseurl}index.php?lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}index.php?lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/home.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{back_to_series}</title>
    <id>{baseurl}index.php?prefix=authors&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}index.php?prefix=series&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/bookseries.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{sort_alpha}</title>
    <id>baseurl}?prefix=series_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}index.php?prefix=series_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/alpha.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{sort_author}</title>
    <id>{baseurl}index.php?prefix=series_id&amp;sort_order=author&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}index.php?prefix=series_id&amp;sort_order=author&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/authors.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{sort_index}</title>
    <id>{baseurl}index.php?prefix=series_id&amp;sort_order=index&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{relurl}index.php?prefix=series_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/bookseries.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

<!-- BEGIN itemblock -->
  <entry>
    <title>{title_by_author}</title>
    <id>{baseurl}{bid}.opds</id>
    <content type="text">ISBN: {isbn}</content>
    <link type="application/atom+xml;type=entry;profile=opds-catalog" href="{baseurl}index.php?action=bookdetails&amp;book={bid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/book.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>
<!-- END itemblock -->

</feed>