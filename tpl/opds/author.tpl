<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:dcterms="http://purl.org/dc/terms/"
      xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/"
      xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/">

  <id>{baseurl}?prefix=author_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset={offset}&amp;lang={lang}</id>
  <title>{books_by_whom}</title>
  <updated>{pubdate}</updated>
  <subtitle>miniCalOPe.</subtitle>

  <author>
    <name>{owner}</name>
    <uri>{homepage}</uri>
    <email>{email}</email>
  </author>

  <link rel="self" title="{this_page}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?prefix=author_id&amp;sort_order={sortorder}&amp;query={aid}&amp;offset={offset}&amp;lang={lang}"/>

<!-- BEGIN prevblock -->
  <link rel="first" title="{first_page}" type="application/atom+xml" href="{baseurl}?prefix=author_id&amp;query={aid}&amp;sort_order={sortorder}&amp;offset=0"/>
  <link rel="previous" title="{prev_page}" type="application/atom+xml" href="{baseurl}?prefix=author_id&amp;query={aid}&amp;sort_order={sortorder}&amp;offset={poffset}"/>
<!-- END prevblock -->
<!-- BEGIN nextblock -->
  <link rel="next" title="{next_page}" type="application/atom+xml" href="{baseurl}?prefix=author_id&amp;query={aid}&amp;sort_order={sortorder}&amp;offset={noffset}"/>
  <link rel="last" title="{last_page}" type="application/atom+xml" href="{baseurl}?prefix=author_id&amp;query={aid}&amp;sort_order={sortorder}&amp;offset={loffset}"/>
<!-- END nextblock -->

  <link rel="http://opds-spec.org/sort/start" title="{start_page}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?lang={lang}"/>
  <link rel="http://opds-spec.org/sort/new" title="{sort_date}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}/?prefix=author_id&amp;sort_order=date&amp;query={aid}&amp;lang={lang}"/>
  <link rel="alternate" title="{sort_title}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}/?prefix=author_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}"/>
  <link rel="up" title="{back_to_authors}"
        type="application/atom+xml;profile=opds-catalog"
        href="{relurl}?prefix=authors&amp;lang={lang}"/>
  <link rel="search"
        type="application/opensearchdescription+xml"
        href="{baseurl}?lang={lang}&amp;prefix=ods"/>
  <link rel="search"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?lang={lang}&amp;pageformat=opds&amp;prefix=searchresults&amp;q={searchTerms}"/>

  <opensearch:totalResults>{total}</opensearch:totalResults>
  <opensearch:startIndex>{start}</opensearch:startIndex>
  <opensearch:itemsPerPage>{per_page}</opensearch:itemsPerPage>

  <entry>
    <title>{start_page}</title>
    <id>{baseurl}?lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/home.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{back_to_authors}</title>
    <id>{baseurl}?prefix=authors&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?prefix=authors&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/authors.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{sort_alpha}</title>
    <id>baseurl}?prefix=author_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?prefix=author_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/alpha.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{sort_date}</title>
    <id>{baseurl}?prefix=author_id&amp;sort_order=date&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?prefix=author_id&amp;sort_order=date&amp;query={aid}&amp;lang={lang}"/>
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