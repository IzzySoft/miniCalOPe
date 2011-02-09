<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:dcterms="http://purl.org/dc/terms/"
      xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/"
      xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/">

  <title>{title_list}</title>
  <subtitle>miniCalOPe</subtitle>
  <id>{baseurl}?prefix=titels&amp;sort_order={sortorder}&amp;lang={lang}&amp;offset={offset}</id>
  <updated>{pubdate}</updated>

  <author>
    <name>{owner}</name>
    <uri>{homepage}</uri>
    <email>{email}</email>
  </author>

  <!--link rel="search" type="application/opensearchdescription+xml" title="Catalog Search" href="{baseurl}osd-authors.xml"/-->
  <link rel="self" title="{this_page}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?prefix=titels&amp;sort_order={sortorder}&amp;offset={offset}"/>
<!-- BEGIN prevblock -->
  <link rel="first" title="{first_page}" type="application/atom+xml" href="{baseurl}?prefix={prefix}{searchvals}&amp;sort_order={sortorder}&amp;offset=0"/>
  <link rel="previous" title="{prev_page}" type="application/atom+xml" href="{baseurl}?prefix={prefix}{searchvals}&amp;sort_order={sortorder}&amp;offset={poffset}"/>
<!-- END prevblock -->
<!-- BEGIN nextblock -->
  <link rel="next" title="{next_page}" type="application/atom+xml" href="{baseurl}?prefix={prefix}{searchvals}&amp;sort_order={sortorder}&amp;offset={noffset}"/>
  <link rel="last" title="{last_page}" type="application/atom+xml" href="{baseurl}?prefix={prefix}{searchvals}&amp;sort_order={sortorder}&amp;offset={loffset}"/>
<!-- END nextblock -->
  <link rel="http://opds-spec.org/sort/start" title="{start_page}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?lang={lang}"/>
  <link rel="alternate" title="{sort_alpha}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?prefix={prefix}{searchvals}&amp;sort_order=title&amp;lang={lang}"/>
  <link rel="alternate" title="{sort_author}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?prefix={prefix}{searchvals}&amp;sort_order=name&amp;lang={lang}"/>
  <link rel="http://opds-spec.org/sort/new" title="{sort_date}"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?prefix={prefix}{searchvals}&amp;sort_order=time&amp;lang={lang}"/>

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
    <title>{sort_alpha}</title>
    <id>{baseurl}?prefix={prefix}{searchvals}&amp;sort_order=title&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?prefix={prefix}{searchvals}&amp;sort_order=title&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/alpha.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{sort_author}</title>
    <id>{baseurl}?prefix={prefix}{searchvals}&amp;sort_order=name&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?prefix={prefix}{searchvals}&amp;sort_order=name&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/authors.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>{sort_date}</title>
    <id>{baseurl}?prefix={prefix}{searchvals}&amp;sort_order=time&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?prefix={prefix}{searchvals}&amp;sort_order=time&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/date.png" rel="http://opds-spec.org/image/thumbnail"/>
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