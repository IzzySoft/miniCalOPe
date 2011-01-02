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
        href="{rel_url}?default_prefix=authors&amp;lang={lang}"/>

  <entry>
    <title>Gehe an den Start</title>
    <id>{baseurl}?lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Zurück zu allen Autoren</title>
    <id>{baseurl}?default_prefix=authors&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=authors&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/image/thumbnail"/>
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
    <link type="image/jpeg" href="{baseurl}{cover_src}" rel="http://opds-spec.org/cover" />
    <link type="image/jpeg" href="{baseurl}{cover_src}" rel="http://opds-spec.org/image" />
<!-- END coverblock -->
    <link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAATbSURBVHjaYvz//z8DDDBZzGVg/POP4R8LKwMrEyMDI8M/BmZODgZGRkaG/x+/Mvzj4tBW15YvenNz14dn+5rrGVi4v4A1/vkKNwNmHkAAMTHgAoxA/Pcfw+9ffxl+ffttpGmpPLe8zPHQ3Ik2SY6WCsEMTOLKDGrxDAwcIli1AwQQC3Yz/zP8/vqLhZmH01xLTzol0E871NZIjJuFGeii3wwM8kpqogyKPhEMUuZGDPfXbgZqeYNuBkAAMSIHBaPZHAaG70CdnBy2+paq+YF+mp7W+sJcbEDXf/7OwPDzDwMDOxsDw8N77/5ffv6LcduND38edQfmMny+MQM9KAACCMXFTGysLpqWmgWhflouFvqC7BxAAz8Bg+/DXwaGf0D1QIrh2w+g73l5GD2YnjN8/cHBsphHwRRo8BxQSCObBRBAKAbrOBv0VqTq60mwAw0DGvgRaNLf/xAMjFMIDVT3j4mZ4eXn/wyKYkCF4hpKDM93CAGFXyGbBRBAKJH38/Wrqx8+MzC8/vKf4RswRL4DTfkGxF+B+PtfCAaJf2NgZvjLysmgIsDAwCOvq8bAwCKHHsYAAYRi8OtLBy4+evsPaAAjxBCoYT+gFoANBVkCdP2Xf8wMwix/GGQUZEQZGLgU0Q0GCCAUgz8/u3X59bufv3/8gxgCMhAUl9+ghoMs+Qk1+PlvVmDK+cmgqirFysApr4puMEAAoRj8+6/CvWf37zz79h9i2DeoS78j0yDxX8DgYWYHWsTMoCnFw8AgpKQO1M6JbBZAAKFmkK8Pnz+5fffWB1DS+gc17C/EwO9/US35CYzAp58ZGVRFmBlYpTU0gbpFkY0CCCBUg/8xfXz/9M3NL5//gb0LC+dvSIbCMQMrw6NvbAzSPCwMfHLK0gwMzDLIRgEEEKrBvx8zfHp2+8LPr98YfvxFM+gPwiJQuP8EpvEXwHBmATpAWkFJCORsZKMAAgjFYHbdUIav/8Wvv37x9s2Pv0jB8AfhcpgFP4Hx8PIPC8O7zz8ZdOUEOBi4ZbWRzQMIINR0/Og8w9/7px5+fP7yHYj/Bcm1P35DXApm/4GmDk4uhpdfGRl05YEJWlARFIECMLMAAgg1KF6dAhYnJ149v//w4T+gq76AkhxS6gCxf4KSJdC1z7/+Z3jy6S/DpYc/GLSkuRlYJZWVkMMZIIBQSzcJO2A4//795N6T8+Y//rj+Anr1HyMoToFZmRWYaICp5dPbrwys798xaLB8ZLAS+cpgpczB8PQXKwOXtLL4RyY+UA68BDIKIIBYMFI1MxPD92cPr/76AiwsePkZ3nwClhkffzD8e3CPQZflHYOr0B8GIWFgscrCxXDlIyvDqk0fGW7dvc3w9fa5TwyMP+HmAQQQC0bxDKw5fvxkvHjp0pPf//nfsTI8ecQQf3gag+KzEwybg5sYdn2Q+n/3/osv3x7ffM3w8todhne3bjF8fXCJ4f/XG0ADrsJMAggg1PJY1BlUoAIZjCyMMvozedUM4gRZmBmXXahjkPhwj9nhq/aRxz+/bmD49foBw7+v94BaHgPxB+QiE2YeQACBGTCM6ngObgY+BXc+NuHUM+JMVx/LMvy3ZWCYC0qVDHgAzCyAAGLBXecxf2X48W7nn1+fmCa9Y/j3nJHB4QIDwyFobUgQAAQYACmANJDUx0lSAAAAAElFTkSuQmCC" rel="http://opds-spec.org/image/thumbnail"/>
<!-- BEGIN itemblock -->
    <link type="application/{ftype}" rel="http://opds-spec.org/acquisition" title="{ftitle}" length="{flength}" href="{baseurl}?action=getbook&amp;book={id}&amp;format={format}&amp;lang={lang}"/>
<!-- END itemblock -->
  </entry>

</feed>