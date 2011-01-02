<?xml version="1.0" encoding="UTF-8"?>
<feed xmlns="http://www.w3.org/2005/Atom"
      xmlns:dcterms="http://purl.org/dc/terms/"
      xmlns:opensearch="http://a9.com/-/spec/opensearch/1.1/"
      xmlns:relevance="http://a9.com/-/opensearch/extensions/relevance/1.0/">

  <title>Bücher mit dem Tag {tag_name}</title>
  <subtitle>miniCalOPe.</subtitle>
  <id>{baseurl}?default_prefix=author_id&amp;sort_order=downloads&amp;query=35&amp;lang={lang}</id>
  <updated>{pubdate}</updated>

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
  <link rel="alternate" title="Nach Autor sortieren"
        type="application/atom+xml;profile=opds-catalog"
        href="{baseurl}?default_prefix=tag_id&amp;sort_order=author&amp;query={aid}&amp;lang={lang}"/>
  <link rel="up" title="Zurück zu allen Tags"
        type="application/atom+xml;profile=opds-catalog"
        href="{rel_url}?default_prefix=tags&amp;lang={lang}"/>

  <opensearch:totalResults>{total}</opensearch:totalResults>
  <opensearch:startIndex>{start}</opensearch:startIndex>
  <opensearch:itemsPerPage>{per_page}</opensearch:itemsPerPage>

  <entry>
    <title>Gehe an den Start</title>
    <id>{baseurl}?lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Zurück zu allen Tags</title>
    <id>{baseurl}?default_prefix=authors&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=tags&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAZeSURBVHjaYvz//z8DLQBAALF4TXmyM81V2uHvt28fv/75z/jz93+GL78YGL7/YmC6/JSFbfOk/J6vF2Y3wjSoqGgyNDdPZvj+/RsDBwcnw7592xjmzOmHGMYnx8Br0czw4XAZA0AAsRyalDJdh6tNrzHJSIITKPkBZOg/BoaPvxkYbiy8zfD19j5eoDAbEP8CaebTCWRg+v+bAeRTRkZGfiYmJg6g8Et0FwMEENPX2zs3dKcH+cY07b74CWjgl58MDKysDAw8QPzh9fPvDF+fnQYbysjEwOfUyMCoFsrw/usfBmYmRgYhIb70T5/e5APlWUGGMXOJAy2DGAwQQExgkpv7zI2rl9b9+AuUhEowAGlGRmZgBDCDXSri1sIgqOPPcOPZJ4YjTzkY5CUE1e7cvuq1ccPyswzswr95tGIY+MyrGGBxBhBALNIhsxn+sgkyCMpL/AX6EGwgDPz/+w/I+/6Hz7WdgUHJm+HT+9cMv379Z7j5lonhydvPaXNn9R37zmu2ncc4m4GZWxIo94WB4c8vkIsYAAKIhVPKlOHnr98MTKzsTAz//jPA/AIif3/9/FVAK8A2KCrC+f+v3yx//kj8//WH4S///28Cb35/cec2SjjlLqTcySKk8f/n14+MP/4IsD6+//PFx/8MMwACiOUPMHb//P7H8PcXM0rg/4eE05/PzJIKNtrivqGGnBw/gT768fMvw52bDxkUlLUYouId/L59Z2D4/P0/AxsLI8OVpwwMLR13Xj/8/v4EQACx/P/9l+E/0GAw/o8Iin9AQ35xy4pEpqQ6S/Aws1x49AfsoZu3XzLcef6PIUuVmYH1D1AhCAN9yc/FwMAOTDtf3z4FhsfP9wABxPL371+Gv3+BLgbS/xmBOlkZwU5lAsbzt3dPvv7+9+u3h6YeCzBoGT58+cbw5uYzhn9CvAzAoGH4+o2d4QfQxT+AjvgJNPTvd5CLGIFpi+EnQACx/AMaCsLAiGJgBGYOcBj8gwQ1178vXzetWHngWJJbkKUyJ8e9K88Z3v8WYPj2loPh0mkuhs9At335CDT4KwODlTUDw7vDQK0fwKmCESCAWP4C/fz3P9DFf34zMJ8C+mvXVwYuIWBW4f3PIHz8K+v3p/dPrj9xy+Ib9xela5+eMHz8qssgzwxMAb+ZGIDRw/AdaOgPaGL4C2SDghYEAAKI5f+fv2DXgmiGz8DcdBeY+N8Caa7vDP/usTIq+QR431WZLtV66xXDS7YXwLBUZPB+3sigzanCwAKMbyZQsDFDfAjMQ8B4gBgMEEBM/4AGQvA/cNgycDIy/OdiZPgNDHMZY31h7dovbq9ZrnL8/MHK8PXfJ4brTGcY1nC0MFx99BCS3RgQGQoU+f//QjIIQAAxgQz8B0wRIBqWa5iAMfVekpfhS/Yhxnv/NzH8fqbC8J7pKcNfYAz9+cTLcJnhBMNOpikMr15+YWBlQS0jwD4HAoAAYvoHTPFg/PsPRACU3L4wM5w2vs+wT2oxw9e7ygzvfn1i+PD9MzCimBh+/AD66AcbwznOzQxnPm9j+PMdEhxwg/9BHAcQQEyQdAzB/4CCrMDweMvAzLAV6NJ3j7kYxP9YMLz+8ZTh3Xtmhi/ASAIFGwPIEcBkeZlhI8Ob90DVTIjg+PcP4mKAAGL6BVT06/dvYI76/RdkMBNQ9jPHL4Z7Px8yvH//nuHW59MMn4Cl2e/vjJB4+PaJgQdYhNhJWTB8/P6I4dP3jwzg5M8KyVv//0BcDBBATN+Biv79ZWQQ5uESEhPgZuAEZh+R28wMul/UgSX3C4b3364BM8MPYDYE+vnbRwYuYNHsq+nNwPvzEwP/HzEGWTEJBi5grnvxGOiRHyBX/wObCxBALIy/PwPD5R8r6z9B0wn7LzJ8fPuV4dvTnwxvN6oxmIeEMNz/d5jhK7DU+v/7JzAsGRlkeUQZHj06zfD89h8G4UfJDJf/3wQXYt9OAiuHj78Y3r4/DCwxGN4BBBAjo6gxJKl8+2oLrG9sGVhByQ4YJr85/kvpeZoL60hbsfJ95mNk+8byFxhkn9/8+PflBdPPr3c/3f/y9Nxuhv9fXzOAQvA/MCUz/gXSr88C09xugAACGmwCDvX/7y4Bs85PtAqGGehJfn0GJh55JiYWHpAB//79+QM0DJiR390FOucGUNEPbJUpQIABACZh2Kq22kvtAAAAAElFTkSuQmCC" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Alphabetisch sortieren</title>
    <id>baseurl}?default_prefix=tag_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=tag_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAMISURBVHjaYvz//z8DLQBAADEQazArK2uIhITEXSDTnRj1AAFErMEcmpqam8rLy//z8/PvBPL5CGkACCAmIj3mqqWl5SYvL88AtMAKxCekASCAiDGYWVpaOktZWZn9/fv3IIN5ODg4IoDi7Pg0AQQQMQZ7GRgYeLx+/Zphz549DGJiYgxAlzsDxU3waQIIIIIGi4iIJIGC4MqVK79Pnjz55e3btwyqqqqCjIyMIFcz4tIHEECEDHYDGuLy6dMnhrt37z789u3b2uvXr/8GWsYgKirqD5TXx6URIIDwGcwOTAFZQK/z3Lx5k+Hdu3cXgWLdd+7cOf/582cGBQUFWSA/GpdmgADCZ7CjlJSUz48fP0Cu/QLk7wXiq8Cg2PT48WMGXl5eBqDFPkAxDWyaAQIIl8GMXFxcuQICAsxPnjwBufYCUOwASOLPnz87nj59+uL3798MQkJCIEN9sRkAEEC4DHYHanL++fMnw4sXL/4C+TuA+B5U7uybN28OAi1jACY7BnZ29nCgmBS6AQABhM1gFjY2tmQgZgcawPDx48fbQLE9QPwTpgBo4ZZXr14x/P37lwHoMyNQkkQ3BCCAsGVpd2D4fQWWC/+5ublBklOwZAYeoEuviIuL/wemkP9MTEygYBJFVgAQQCzorgUqygC6hAuUxIAR9wYodguIZYCYDUndl1+/fh0F+kabmZmZAYjN//375wEUXwxTABBA6C52ASb8v0DD/wNpEP4LxN+B+CcQ/0LCIP4PkBqQWhAG6t0KxNwwgwACCMVgoMLlUANB+A9Q6CsQf8OCv0PlfsHUg3wBxM4wswACCDkonJAjAWjhMSA1F4g/gwoiLJH8A4h1gLgKFOZAw7mBeqKA7IOgVAkQQDAXswAlViDZ/guIC4jI8mxA9XuQfPkSKAYqVhkAAghmsDc0LGEGXwJiIyLL6iQkg0F6Z4IcChBAMIMXwMIViH8D+ROAmItIg8WA+DKSwaCMZAwQQIwgg4ECxkCOHqhqA2JQTjsFUkxC1QnSawqNC5D+wwABBgABYwq1dYqKjAAAAABJRU5ErkJggg==" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Nach Autor sortieren</title>
    <id>{baseurl}?default_prefix=tag_id&amp;sort_order=author&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=tag_id&amp;sort_order=author&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Nach Datum sortieren</title>
    <id>{baseurl}?default_prefix=tag_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{rel_url}?default_prefix=tag_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAY9SURBVHjaYvz//z9DeXk5w5/fPxm+fPnHwMjEx/AbyGZkZGT49esHAxsbO8Pfv//VODg41H/9enlbQIBPXkhQ0IeXj891xYoVU44fPz6FAQsACCAWdAGQRUDMxsDAqMzMzGwqJCQUxcT0T0NMTEyejU32lZ6+poCGhiYbSO35CxdSgQZvBzLvopsDEEAoBgON5GFjYaniYGcxFpMQsWJi+s8pJS3OLCIqzqCiIs/AwcolJi8vDrb89dt3DMYmpprr1661/PjpE4bBAAHEAnUl++9fvw3Y2FgylFSkEoTFJBgUlWQZeLl5GZSVZBi+fvnOwM3JyvDj50+Gx08eMwgLCTNwcXExmFtbs2poaHicPHVqFdCYX8gGAwQQ2GB2Do5VhmYWXhraOizycjIMPz5/ZxAWFmR4/+4tw/cvnxnY2VgYvnz7ygAMDoZ/f/8ysLKyMjAC9cnJKjAYmZk7Ag3WAnIvIBsMEEBMIIKLl9/MxcOLRVtJjoGTCRhpP78yfP36iYGbm4uBi4eHQVBQiEFSQoKBgYmJgRlo6N//IF8yAF3Ox2BiaSMlISbmgB4UAAEENvjPj2+v3n78wvAaiP/++88gLSPNwMfPz8DDw83AzcrC8O/efQaGM2cYmE+cZGC4fYuB4e9vhr9Q7+qamjNo6uj4AJlCyAYDBBDY4Pt37x578fINA7+YCNiVDEzMDP+Bye3vi5cMTIcOMzBfv8YATIsMjM+fMTCfPMXAeO0GUB5igLycLIOuoYkeJzubEbLBAAEENvjM2dN779+6xfDmK8SLYAwUZ/z8meEfBwfDb1tbhl8ODgx/DA0ZGIARyPTyBThl/AEqEmBnYjCycxGVkJDwQDYYIIDABv/79+/s9Uvnbz17+4PhJ8hAkMFAv/5RUWH4bW3N8I+PD+h9oMCHjwzA3MPwV0qS4R/QR8BQY2AFqlfX0WXQ0DEAhbMUzGCAAAIb/OXzl/uXz504/ODeQ4YPf4AhwQh19V8o/ecvA8vevQws+/aCXcx89hwD87kLDP9AKR+UOqQkGAxsHA24OdmtYAYDBBDYYDZ2NoZPH19vv3Hh7K9HQEf9hWcYqMHAMP+rpMzwy9ub4beVNQPD69cMrJs3MjABI/I3UI0ABwODlqkFs7SMvCfIOJBegAACGwzMugwvX7w6dP/iiZt3n35j+PoPKAYz9B+E/qOizPBHXZ3hl6kJw18tbQaGd+8YGB8+BjuCHRQc2voMGoYmziAmyEyAAIIEBTDGv33//vrZvYvbb125yfAQGImMTFBng4MF6Olfv4GW/GdgevKUgfnyZaC72IC+UAIGByT45cU5GbQt7OVFBPlcQdoAAgic89iAif4f0NWPHt3Z+PjyyaLrJoYsGvygWAUioArWQ0cZ2I4eYfgPNIzp6TOG/1wcDD+joxn+qSgxsAENZQZaLgLEevauDCrrDILeHDm0HCCAwAb/ArkGVKox/j3/4vqRoxevB9iby0swyAKj/AfISUDn/+XmYWAE5sI/lpYMDDraDMx8/Ay/gYa+ApYQjz//Z3j0hZHh2R8xBhlFdV2Ok0dNAQIIUroB0xcw9TD8/PPv+5M7Z9bxX7pgf87IgwGYARmYgJr/2lozMAIxCHwD4pfAoLr3BFhWvv3P8OQdI8OrN4wMnz4A1X76xvDz2292JiZGQYAAAhv87v1HsKa/wMBi/v9n15vLu++dvOWiZC/FwiAMVPEKmLiffmFguPUeZBgDw6PXDAwvgPgLkP//0zsG1neXGJjeHWf4/vzkl2cPrlz48fvfI4AAAhssAizJYODPn983Ptw7su/WuZtKW1W0GRiBXr30FJjtXwJT2RsGhq9v/zAwfXzKwPHhLAPT25N/f7w8/frd86tX373/cO7D51/HgAnoEtCYpwABxAgKWx1NZeQqhIGF8a+HgEneht+2xewfgF78CXQy+8ebDJwfTzMwvz/558fLMw8+vLp3+d3Hj6e+/mA4BTQMWJgwvGZAZAEGgAACGyzAz4NS5DH+/82ub+69/7l4niXTx1sMnG+3f/r17sqj92+fXnzz/vuRX38ZzgKV3QHij5C0gwkAAghsMB8vL2q9B8T8/LzuwEoj5dOHN8/ff/59DJgCLgKFH4OSPQMRACDAABLoZ3R+p3OCAAAAAElFTkSuQmCC" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

<!-- BEGIN itemblock -->
  <entry>
    <title>{title} by {author}</title>
    <id>{baseurl}{bid}.opds</id>
    <content type="text">ISBN: {isbn}</content>
    <link type="application/atom+xml;type=entry;profile=opds-catalog" href="{baseurl}?action=bookdetails&amp;book={bid}&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAATbSURBVHjaYvz//z8DDDBZzGVg/POP4R8LKwMrEyMDI8M/BmZODgZGRkaG/x+/Mvzj4tBW15YvenNz14dn+5rrGVi4v4A1/vkKNwNmHkAAMTHgAoxA/Pcfw+9ffxl+ffttpGmpPLe8zPHQ3Ik2SY6WCsEMTOLKDGrxDAwcIli1AwQQC3Yz/zP8/vqLhZmH01xLTzol0E871NZIjJuFGeii3wwM8kpqogyKPhEMUuZGDPfXbgZqeYNuBkAAMSIHBaPZHAaG70CdnBy2+paq+YF+mp7W+sJcbEDXf/7OwPDzDwMDOxsDw8N77/5ffv6LcduND38edQfmMny+MQM9KAACCMXFTGysLpqWmgWhflouFvqC7BxAAz8Bg+/DXwaGf0D1QIrh2w+g73l5GD2YnjN8/cHBsphHwRRo8BxQSCObBRBAKAbrOBv0VqTq60mwAw0DGvgRaNLf/xAMjFMIDVT3j4mZ4eXn/wyKYkCF4hpKDM93CAGFXyGbBRBAKJH38/Wrqx8+MzC8/vKf4RswRL4DTfkGxF+B+PtfCAaJf2NgZvjLysmgIsDAwCOvq8bAwCKHHsYAAYRi8OtLBy4+evsPaAAjxBCoYT+gFoANBVkCdP2Xf8wMwix/GGQUZEQZGLgU0Q0GCCAUgz8/u3X59bufv3/8gxgCMhAUl9+ghoMs+Qk1+PlvVmDK+cmgqirFysApr4puMEAAoRj8+6/CvWf37zz79h9i2DeoS78j0yDxX8DgYWYHWsTMoCnFw8AgpKQO1M6JbBZAAKFmkK8Pnz+5fffWB1DS+gc17C/EwO9/US35CYzAp58ZGVRFmBlYpTU0gbpFkY0CCCBUg/8xfXz/9M3NL5//gb0LC+dvSIbCMQMrw6NvbAzSPCwMfHLK0gwMzDLIRgEEEKrBvx8zfHp2+8LPr98YfvxFM+gPwiJQuP8EpvEXwHBmATpAWkFJCORsZKMAAgjFYHbdUIav/8Wvv37x9s2Pv0jB8AfhcpgFP4Hx8PIPC8O7zz8ZdOUEOBi4ZbWRzQMIINR0/Og8w9/7px5+fP7yHYj/Bcm1P35DXApm/4GmDk4uhpdfGRl05YEJWlARFIECMLMAAgg1KF6dAhYnJ149v//w4T+gq76AkhxS6gCxf4KSJdC1z7/+Z3jy6S/DpYc/GLSkuRlYJZWVkMMZIIBQSzcJO2A4//795N6T8+Y//rj+Anr1HyMoToFZmRWYaICp5dPbrwys798xaLB8ZLAS+cpgpczB8PQXKwOXtLL4RyY+UA68BDIKIIBYMFI1MxPD92cPr/76AiwsePkZ3nwClhkffzD8e3CPQZflHYOr0B8GIWFgscrCxXDlIyvDqk0fGW7dvc3w9fa5TwyMP+HmAQQQC0bxDKw5fvxkvHjp0pPf//nfsTI8ecQQf3gag+KzEwybg5sYdn2Q+n/3/osv3x7ffM3w8todhne3bjF8fXCJ4f/XG0ADrsJMAggg1PJY1BlUoAIZjCyMMvozedUM4gRZmBmXXahjkPhwj9nhq/aRxz+/bmD49foBw7+v94BaHgPxB+QiE2YeQACBGTCM6ngObgY+BXc+NuHUM+JMVx/LMvy3ZWCYC0qVDHgAzCyAAGLBXecxf2X48W7nn1+fmCa9Y/j3nJHB4QIDwyFobUgQAAQYACmANJDUx0lSAAAAAElFTkSuQmCC" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>
<!-- END itemblock -->

</feed>