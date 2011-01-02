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
    <link type="application/atom+xml;profile=opds-catalog" href="{rel_url}?default_prefix=authors&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Titel</title>
    <id>{baseurl}?default_prefix=titles&amp;lang={lang}</id>
    <content type="text">Suche nach Titeln.</content>
    <link type="application/atom+xml;profile=opds-catalog" href="{rel_url}?default_prefix=titles&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAATbSURBVHjaYvz//z8DDDBZzGVg/POP4R8LKwMrEyMDI8M/BmZODgZGRkaG/x+/Mvzj4tBW15YvenNz14dn+5rrGVi4v4A1/vkKNwNmHkAAMTHgAoxA/Pcfw+9ffxl+ffttpGmpPLe8zPHQ3Ik2SY6WCsEMTOLKDGrxDAwcIli1AwQQC3Yz/zP8/vqLhZmH01xLTzol0E871NZIjJuFGeii3wwM8kpqogyKPhEMUuZGDPfXbgZqeYNuBkAAMSIHBaPZHAaG70CdnBy2+paq+YF+mp7W+sJcbEDXf/7OwPDzDwMDOxsDw8N77/5ffv6LcduND38edQfmMny+MQM9KAACCMXFTGysLpqWmgWhflouFvqC7BxAAz8Bg+/DXwaGf0D1QIrh2w+g73l5GD2YnjN8/cHBsphHwRRo8BxQSCObBRBAKAbrOBv0VqTq60mwAw0DGvgRaNLf/xAMjFMIDVT3j4mZ4eXn/wyKYkCF4hpKDM93CAGFXyGbBRBAKJH38/Wrqx8+MzC8/vKf4RswRL4DTfkGxF+B+PtfCAaJf2NgZvjLysmgIsDAwCOvq8bAwCKHHsYAAYRi8OtLBy4+evsPaAAjxBCoYT+gFoANBVkCdP2Xf8wMwix/GGQUZEQZGLgU0Q0GCCAUgz8/u3X59bufv3/8gxgCMhAUl9+ghoMs+Qk1+PlvVmDK+cmgqirFysApr4puMEAAoRj8+6/CvWf37zz79h9i2DeoS78j0yDxX8DgYWYHWsTMoCnFw8AgpKQO1M6JbBZAAKFmkK8Pnz+5fffWB1DS+gc17C/EwO9/US35CYzAp58ZGVRFmBlYpTU0gbpFkY0CCCBUg/8xfXz/9M3NL5//gb0LC+dvSIbCMQMrw6NvbAzSPCwMfHLK0gwMzDLIRgEEEKrBvx8zfHp2+8LPr98YfvxFM+gPwiJQuP8EpvEXwHBmATpAWkFJCORsZKMAAgjFYHbdUIav/8Wvv37x9s2Pv0jB8AfhcpgFP4Hx8PIPC8O7zz8ZdOUEOBi4ZbWRzQMIINR0/Og8w9/7px5+fP7yHYj/Bcm1P35DXApm/4GmDk4uhpdfGRl05YEJWlARFIECMLMAAgg1KF6dAhYnJ149v//w4T+gq76AkhxS6gCxf4KSJdC1z7/+Z3jy6S/DpYc/GLSkuRlYJZWVkMMZIIBQSzcJO2A4//795N6T8+Y//rj+Anr1HyMoToFZmRWYaICp5dPbrwys798xaLB8ZLAS+cpgpczB8PQXKwOXtLL4RyY+UA68BDIKIIBYMFI1MxPD92cPr/76AiwsePkZ3nwClhkffzD8e3CPQZflHYOr0B8GIWFgscrCxXDlIyvDqk0fGW7dvc3w9fa5TwyMP+HmAQQQC0bxDKw5fvxkvHjp0pPf//nfsTI8ecQQf3gag+KzEwybg5sYdn2Q+n/3/osv3x7ffM3w8todhne3bjF8fXCJ4f/XG0ADrsJMAggg1PJY1BlUoAIZjCyMMvozedUM4gRZmBmXXahjkPhwj9nhq/aRxz+/bmD49foBw7+v94BaHgPxB+QiE2YeQACBGTCM6ngObgY+BXc+NuHUM+JMVx/LMvy3ZWCYC0qVDHgAzCyAAGLBXecxf2X48W7nn1+fmCa9Y/j3nJHB4QIDwyFobUgQAAQYACmANJDUx0lSAAAAAElFTkSuQmCC" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Themen</title>
    <id>{baseurl}?default_prefix=subjects&amp;lang={lang}</id>
    <content type="text">Suche nach Themen.</content>
    <link type="application/atom+xml;profile=opds-catalog" href="{rel_url}?default_prefix=tags&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

</feed>