<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:dcterms="http://purl.org/dc/terms/"
      xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/"
      xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/">

  <title>Alle Tags</title>
  <subtitle>miniCalOPe</subtitle>
  <id>{baseurl}?default_prefix=tags&amp;sort_order={sortorder}&amp;offset={offset}&amp;lang={lang}</id>
  <updated>{pubdate}</updated>

  <author>
    <name>{owner}</name>
    <uri>{homepage}</uri>
    <email>{email}</email>
  </author>

  <!--link rel="search" type="application/opensearchdescription+xml" title="Catalog Search" href="{baseurl}osd-tags.xml"/-->
  <link rel="self" title="Diese Seite"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?default_prefix=tags&amp;sort_order=downloads&amp;lang={lang}"/>
<!-- BEGIN prevblock -->
  <link rel="first" title="Erste Seite" type="application/atom+xml" href="{baseurl}?default_prefix=tags&amp;sort_order={sortorder}&amp;offset=0"/>
  <link rel="previous" title="Vorige Seite" type="application/atom+xml" href="{baseurl}?default_prefix=tags&amp;sort_order={sortorder}&amp;offset={poffset}"/>
<!-- END prevblock -->
<!-- BEGIN nextblock -->
  <link rel="next" title="Nächste Seite" type="application/atom+xml" href="{baseurl}?default_prefix=tags&amp;sort_order={sortorder}&amp;offset={noffset}"/>
  <link rel="last" title="Letzte Seite" type="application/atom+xml" href="{baseurl}?default_prefix=tags&amp;sort_order={sortorder}&amp;offset={loffset}"/>
<!-- END nextblock -->


  <link rel="http://opds-spec.org/sort/start" title="Gehe an den Start"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?lang={lang}"/>
  <link rel="alternate" title="Alphabetisch sortieren" type="application/atom+xml" href="{baseurl}?default_prefix=tags&amp;sort_order=title&amp;lang={lang}"/>
  <link rel="http://opds-spec.org/sort/numerous" title="Nach Anzahl sortieren" type="application/atom+xml" href="{baseurl}?default_prefix=tags&amp;sort_order=books&amp;lang={lang}"/>

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
    <title>Alphabetisch sortieren</title>
    <id>{baseurl}?default_prefix=tags&amp;sort_order=title&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=tags&amp;sort_order=title&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/alpha.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Nach Buchanzahl sortieren</title>
    <id>{baseurl}?default_prefix=tags&amp;sort_order=books&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=tags&amp;sort_order=title&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/alpha.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <!-- BEGIN itemblock -->
    <entry>
    <title>{name}</title>
    <id>{baseurl}?default_prefix=tag_id&amp;sort_order={sortorder}&amp;query={id}&amp;lang={lang}</id>
    <content type="text">{num_books} {books}</content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=tag_id&amp;sort_order=downloads&amp;query={id}&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/tag.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>
<!-- END itemblock -->

</feed>