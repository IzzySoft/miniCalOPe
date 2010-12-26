<?xml version="1.0" encoding="utf-8"?><!--
List of all authors
--><feed xmlns:dcterms="http://purl.org/dc/terms/" xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/" xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/" xmlns="http://www.w3.org/2005/Atom" xml:base="{baseurl}">
<id>{baseurl}?default_prefix=authors&amp;sort_order=downloads</id>
<updated>{pubdate}</updated>
<title>Alle Autoren</title>
<subtitle>Mehr haben wir nicht.</subtitle>

<author>
<name>{owner}</name>
<uri>{homepage}</uri>
<email>{email}</email>
</author>

<!--icon>http://m.gutenberg.org/pics/favicon.png</icon-->
<!--link rel="search" type="application/opensearchdescription+xml" title="Project Gutenberg Catalog Search" href="http://m.gutenberg.org/catalog/osd-authors.xml"/-->
<link rel="self" title="Diese Seite" type="application/atom+xml" href="{baseurl}?default_prefix=authors&amp;sort_order=downloads"/>
<!--link rel="next" title="Nächste Seite" type="application/atom+xml" href="/ebooks/search.opds/?default_prefix=authors&amp;sort_order=downloads&amp;start_index=26"/-->
<link rel="http://opds-spec.org/sort/start" title="Gehe an den Start" type="application/atom+xml" href="{relurl}"/>
<!--link rel="http://opds-spec.org/sort/numerous" title="Nach Anzahl sortieren" type="application/atom+xml" href="/ebooks/search.opds/?default_prefix=authors&amp;sort_order=quantity"/>
<link rel="http://opds-spec.org/sort/new" title="Nach Datum sortieren" type="application/atom+xml" href="/ebooks/search.opds/?default_prefix=authors&amp;sort_order=release_date"/-->

<opensearch:totalResults>{total}</opensearch:totalResults>
<opensearch:startIndex>{start}</opensearch:startIndex>
<opensearch:itemsPerPage>{per_page}</opensearch:itemsPerPage>

<entry>
<updated>{pubdate}</updated>
<id>{baseurl}</id>
<title>Gehe an den Start</title>
<content type="text"></content>
<link type="application/atom+xml" href="{relurl}"/>
<link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/image/thumbnail"/>
</entry>

<entry>
<updated>{pubdate}</updated>
<id>{baseurl}?default_prefix=authors&amp;sort_order=title</id>
<title>Alphabetisch sortieren</title>
<content type="text"></content>
<link type="application/atom+xml" href="{relurl}?default_prefix=authors&amp;sort_order=title"/>
<link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAMISURBVHjaYvz//z8DLQBAADEQazArK2uIhITEXSDTnRj1AAFErMEcmpqam8rLy//z8/PvBPL5CGkACCAmIj3mqqWl5SYvL88AtMAKxCekASCAiDGYWVpaOktZWZn9/fv3IIN5ODg4IoDi7Pg0AQQQMQZ7GRgYeLx+/Zphz549DGJiYgxAlzsDxU3waQIIIIIGi4iIJIGC4MqVK79Pnjz55e3btwyqqqqCjIyMIFcz4tIHEECEDHYDGuLy6dMnhrt37z789u3b2uvXr/8GWsYgKirqD5TXx6URIIDwGcwOTAFZQK/z3Lx5k+Hdu3cXgWLdd+7cOf/582cGBQUFWSA/GpdmgADCZ7CjlJSUz48fP0Cu/QLk7wXiq8Cg2PT48WMGXl5eBqDFPkAxDWyaAQIIl8GMXFxcuQICAsxPnjwBufYCUOwASOLPnz87nj59+uL3798MQkJCIEN9sRkAEEC4DHYHanL++fMnw4sXL/4C+TuA+B5U7uybN28OAi1jACY7BnZ29nCgmBS6AQABhM1gFjY2tmQgZgcawPDx48fbQLE9QPwTpgBo4ZZXr14x/P37lwHoMyNQkkQ3BCCAsGVpd2D4fQWWC/+5ublBklOwZAYeoEuviIuL/wemkP9MTEygYBJFVgAQQCzorgUqygC6hAuUxIAR9wYodguIZYCYDUndl1+/fh0F+kabmZmZAYjN//375wEUXwxTABBA6C52ASb8v0DD/wNpEP4LxN+B+CcQ/0LCIP4PkBqQWhAG6t0KxNwwgwACCMVgoMLlUANB+A9Q6CsQf8OCv0PlfsHUg3wBxM4wswACCDkonJAjAWjhMSA1F4g/gwoiLJH8A4h1gLgKFOZAw7mBeqKA7IOgVAkQQDAXswAlViDZ/guIC4jI8mxA9XuQfPkSKAYqVhkAAghmsDc0LGEGXwJiIyLL6iQkg0F6Z4IcChBAMIMXwMIViH8D+ROAmItIg8WA+DKSwaCMZAwQQIwgg4ECxkCOHqhqA2JQTjsFUkxC1QnSawqNC5D+wwABBgABYwq1dYqKjAAAAABJRU5ErkJggg==" rel="http://opds-spec.org/image/thumbnail"/>
</entry>

<!-- BEGIN itemblock -->
<entry>
<updated>{pubdate}</updated>
<id>{baseurl}?default_prefix=author_id&amp;sort_order=downloads&amp;query={id}</id>
<title>{name}</title>
<content type="text">{num_books} {books}</content>
<link type="application/atom+xml" href="{relurl}?default_prefix=author_id&amp;sort_order=downloads&amp;query={id}"/>
<link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/image/thumbnail"/>
</entry>
<!-- END itemblock -->

</feed>