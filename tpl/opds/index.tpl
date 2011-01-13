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
  <title>{site_title}</title>
  <updated>{pubdate}</updated>
  <author>
    <name>{owner}</name>
    <uri>{homepage}</uri>
    <email>{email}</email>
  </author>

  <entry>
    <title>Autoren</title>
    <id>{baseurl}?default_prefix=authors&amp;lang={lang}</id>
    <content type="text">Suche nach Autoren.</content>
    <link type="application/atom+xml;profile=opds-catalog" href="{relurl}?default_prefix=authors&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/authors.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Titel</title>
    <id>{baseurl}?default_prefix=titles&amp;lang={lang}</id>
    <content type="text">Suche nach Titeln.</content>
    <link type="application/atom+xml;profile=opds-catalog" href="{relurl}?default_prefix=titles&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/bookcase.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Themen</title>
    <id>{baseurl}?default_prefix=subjects&amp;lang={lang}</id>
    <content type="text">Suche nach Themen.</content>
    <link type="application/atom+xml;profile=opds-catalog" href="{relurl}?default_prefix=tags&amp;lang={lang}"/>
    <link type="image/png" href="{relurl}tpl/icons/tags.png" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

</feed>