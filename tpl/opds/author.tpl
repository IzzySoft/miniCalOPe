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
        href="{rel_url}?default_prefix=authors&amp;lang={lang}"/>

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
    <title>Zurück zu allen Autoren</title>
    <id>{baseurl}?default_prefix=authors&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" href="{baseurl}?default_prefix=authors&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,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" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Alphabetisch sortieren</title>
    <id>baseurl}?default_prefix=author_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" rel="alternate" href="{baseurl}?default_prefix=author_id&amp;sort_order=title&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAMISURBVHjaYvz//z8DLQBAADEQazArK2uIhITEXSDTnRj1AAFErMEcmpqam8rLy//z8/PvBPL5CGkACCAmIj3mqqWl5SYvL88AtMAKxCekASCAiDGYWVpaOktZWZn9/fv3IIN5ODg4IoDi7Pg0AQQQMQZ7GRgYeLx+/Zphz549DGJiYgxAlzsDxU3waQIIIIIGi4iIJIGC4MqVK79Pnjz55e3btwyqqqqCjIyMIFcz4tIHEECEDHYDGuLy6dMnhrt37z789u3b2uvXr/8GWsYgKirqD5TXx6URIIDwGcwOTAFZQK/z3Lx5k+Hdu3cXgWLdd+7cOf/582cGBQUFWSA/GpdmgADCZ7CjlJSUz48fP0Cu/QLk7wXiq8Cg2PT48WMGXl5eBqDFPkAxDWyaAQIIl8GMXFxcuQICAsxPnjwBufYCUOwASOLPnz87nj59+uL3798MQkJCIEN9sRkAEEC4DHYHanL++fMnw4sXL/4C+TuA+B5U7uybN28OAi1jACY7BnZ29nCgmBS6AQABhM1gFjY2tmQgZgcawPDx48fbQLE9QPwTpgBo4ZZXr14x/P37lwHoMyNQkkQ3BCCAsGVpd2D4fQWWC/+5ublBklOwZAYeoEuviIuL/wemkP9MTEygYBJFVgAQQCzorgUqygC6hAuUxIAR9wYodguIZYCYDUndl1+/fh0F+kabmZmZAYjN//375wEUXwxTABBA6C52ASb8v0DD/wNpEP4LxN+B+CcQ/0LCIP4PkBqQWhAG6t0KxNwwgwACCMVgoMLlUANB+A9Q6CsQf8OCv0PlfsHUg3wBxM4wswACCDkonJAjAWjhMSA1F4g/gwoiLJH8A4h1gLgKFOZAw7mBeqKA7IOgVAkQQDAXswAlViDZ/guIC4jI8mxA9XuQfPkSKAYqVhkAAghmsDc0LGEGXwJiIyLL6iQkg0F6Z4IcChBAMIMXwMIViH8D+ROAmItIg8WA+DKSwaCMZAwQQIwgg4ECxkCOHqhqA2JQTjsFUkxC1QnSawqNC5D+wwABBgABYwq1dYqKjAAAAABJRU5ErkJggg==" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Nach Datum sortieren</title>
    <id>{baseurl}?default_prefix=author_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}</id>
    <content type="text"></content>
    <link type="application/atom+xml;profile=opds-catalog" rel="alternate" href="{baseurl}?default_prefix=author_id&amp;sort_order=release_date&amp;query={aid}&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAY9SURBVHjaYvz//z9DeXk5w5/fPxm+fPnHwMjEx/AbyGZkZGT49esHAxsbO8Pfv//VODg41H/9enlbQIBPXkhQ0IeXj891xYoVU44fPz6FAQsACCAWdAGQRUDMxsDAqMzMzGwqJCQUxcT0T0NMTEyejU32lZ6+poCGhiYbSO35CxdSgQZvBzLvopsDEEAoBgON5GFjYaniYGcxFpMQsWJi+s8pJS3OLCIqzqCiIs/AwcolJi8vDrb89dt3DMYmpprr1661/PjpE4bBAAHEAnUl++9fvw3Y2FgylFSkEoTFJBgUlWQZeLl5GZSVZBi+fvnOwM3JyvDj50+Gx08eMwgLCTNwcXExmFtbs2poaHicPHVqFdCYX8gGAwQQ2GB2Do5VhmYWXhraOizycjIMPz5/ZxAWFmR4/+4tw/cvnxnY2VgYvnz7ygAMDoZ/f/8ysLKyMjAC9cnJKjAYmZk7Ag3WAnIvIBsMEEBMIIKLl9/MxcOLRVtJjoGTCRhpP78yfP36iYGbm4uBi4eHQVBQiEFSQoKBgYmJgRlo6N//IF8yAF3Ox2BiaSMlISbmgB4UAAEENvjPj2+v3n78wvAaiP/++88gLSPNwMfPz8DDw83AzcrC8O/efQaGM2cYmE+cZGC4fYuB4e9vhr9Q7+qamjNo6uj4AJlCyAYDBBDY4Pt37x578fINA7+YCNiVDEzMDP+Bye3vi5cMTIcOMzBfv8YATIsMjM+fMTCfPMXAeO0GUB5igLycLIOuoYkeJzubEbLBAAEENvjM2dN779+6xfDmK8SLYAwUZ/z8meEfBwfDb1tbhl8ODgx/DA0ZGIARyPTyBThl/AEqEmBnYjCycxGVkJDwQDYYIIDABv/79+/s9Uvnbz17+4PhJ8hAkMFAv/5RUWH4bW3N8I+PD+h9oMCHjwzA3MPwV0qS4R/QR8BQY2AFqlfX0WXQ0DEAhbMUzGCAAAIb/OXzl/uXz504/ODeQ4YPf4AhwQh19V8o/ecvA8vevQws+/aCXcx89hwD87kLDP9AKR+UOqQkGAxsHA24OdmtYAYDBBDYYDZ2NoZPH19vv3Hh7K9HQEf9hWcYqMHAMP+rpMzwy9ub4beVNQPD69cMrJs3MjABI/I3UI0ABwODlqkFs7SMvCfIOJBegAACGwzMugwvX7w6dP/iiZt3n35j+PoPKAYz9B+E/qOizPBHXZ3hl6kJw18tbQaGd+8YGB8+BjuCHRQc2voMGoYmziAmyEyAAIIEBTDGv33//vrZvYvbb125yfAQGImMTFBng4MF6Olfv4GW/GdgevKUgfnyZaC72IC+UAIGByT45cU5GbQt7OVFBPlcQdoAAgic89iAif4f0NWPHt3Z+PjyyaLrJoYsGvygWAUioArWQ0cZ2I4eYfgPNIzp6TOG/1wcDD+joxn+qSgxsAENZQZaLgLEevauDCrrDILeHDm0HCCAwAb/ArkGVKox/j3/4vqRoxevB9iby0swyAKj/AfISUDn/+XmYWAE5sI/lpYMDDraDMx8/Ay/gYa+ApYQjz//Z3j0hZHh2R8xBhlFdV2Ok0dNAQIIUroB0xcw9TD8/PPv+5M7Z9bxX7pgf87IgwGYARmYgJr/2lozMAIxCHwD4pfAoLr3BFhWvv3P8OQdI8OrN4wMnz4A1X76xvDz2292JiZGQYAAAhv87v1HsKa/wMBi/v9n15vLu++dvOWiZC/FwiAMVPEKmLiffmFguPUeZBgDw6PXDAwvgPgLkP//0zsG1neXGJjeHWf4/vzkl2cPrlz48fvfI4AAAhssAizJYODPn983Ptw7su/WuZtKW1W0GRiBXr30FJjtXwJT2RsGhq9v/zAwfXzKwPHhLAPT25N/f7w8/frd86tX373/cO7D51/HgAnoEtCYpwABxAgKWx1NZeQqhIGF8a+HgEneht+2xewfgF78CXQy+8ebDJwfTzMwvz/558fLMw8+vLp3+d3Hj6e+/mA4BTQMWJgwvGZAZAEGgAACGyzAz4NS5DH+/82ub+69/7l4niXTx1sMnG+3f/r17sqj92+fXnzz/vuRX38ZzgKV3QHij5C0gwkAAghsMB8vL2q9B8T8/LzuwEoj5dOHN8/ff/59DJgCLgKFH4OSPQMRACDAABLoZ3R+p3OCAAAAAElFTkSuQmCC" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

  <entry>
    <title>Wikipedia</title>
    <id>{wikibase}{wikiauthor}</id>
    <content type="text"></content>
    <link type="application/xhtml+xml" href="{wikibase}{wikiauthor}"/>
    <link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAAbsSURBVHjaYmRgaGMAAUZGRgYmZmaGv3/+A3m/gZjJkE9a2FteTdBcTkFQhVuAk+/b15/fHj/99OjRnSvn3j+6uYPh+4WjDKwMPxhYRRgYvh1gQAYAAcSIbDAI//v3V0FUQbwyMNE4KNxfQURDkY9BmI+BgZ2BCWzdmy//Ga4++c2wYc/jH5sWr937+PyeHgaGDwcYfp9GMRgggBggBoNwKwMjQ4eDTdDuG2vOffj/9t+3/7/+f/r/88/X/7/+/vj//df3/x++vf///tur/19/v/7/5s+3/6uu/fvvVnL+M6tAYB66qQABxMzA4ASk/gMNZXJwSbZeUdSiLW8g/Zbhw4cXDO++fGP4+ecPw59//xhefn7F8OHbB4YvP78xfPj+leHXj3cMsgKfGUQ1eNi+8Jq5P7n07Pef77eOwgwGCCCowf8UrALN1/rlc8gqcz5i+P2LkYGdTYxBmEeKgZeDj4GbnYvhxafnDO++vWX49ecnw/df3xi+/frF8A1oiQDzS4avfPcZ/wv42T06c/rW/78vr4IMBgggJgaGP6wySorV8h6flIT/3WDg41Bj4OXWZeDllAX6g5OBkZmN4euvfwzvPn9mYPzPyfD520+g4cAY+8nM8Or9B4YfP1gYlNm+MvCpXmVVccpvZmBgVQIZDBBATMyMbNqGvkrBFx8eZrjwUIyBmVMBGIkcDN+AMfXzLwPDu6+/GM7cOMfw4uVbhq+f/zH8+83D8OcXJ8P7Dx8Zfv/4x/D81UeGjbuPMBzeP41Bz9VElU3APBJkMEAAAFkApv8EFx0WTfwQ6BEKBj0A5AA6AKXRAAAuFQAAIhMAAADx7gDY18cA9/n0AAMDBADy7+MAAwIEAP39/ADz9/gA2eDhALvIvwDh4+AAAP/7APcu2wAhNE9P2bzEnAKIRURO0OIX83cGbi55Bm5eQYbXPxgYDjxkYLj1AWjwpw8MBzavZWBjYWBgY+cEuYPh24+fDB8+fmB4/OI5w6e/nAx/f/1lYOGRYPjz4RnDg9uHGcQVHRRuMnBpAQQQC78Yl/KHb+cZLu/vYriraMugphfAoKzmwvD8M9AwVnmG/3IuDIcOzwK6mJXh65dXwCj5A0r0QPSfgYtfnIGDjZ9BREwdaCUoKXxj4OJg5GZg4pYACCAWBqZfvB++XGX4y/mN4cXzHQyvXu5n+Pm7Dmi4H8NvoIu0jKOAloUAU8oHhu2bMhnev7nGwMrGDU5Sv389Z5BTCGKQl7Nm+PP3A4OsqBDDryfcLAz/GbkBAojp358/3zl42RhY+FkZuEQEGVj4/jNcvjyR4S0wPH8Bc/eHrwwMP/6yAbOeGIOQpCYDM88/Bmagub+Z3zFIq1gyOLk1MshIqzKIcLEx2BrYMTD/Z/4PDO9/AAHE9PX1r4c8PHwMbMBsy8j1m+Ev+2cGPaMUBlERJYY/wJTxD2j4L6Dv/wBTCDsfPwMrz3+wOmEpNQZLi3wGIQ4Whqcv9zJw8AJLF6ZvDK8fvwDG0vdvAAHE9P7Z/bPSvAYMTLw/GTj4OBmsjXMZdFX8GbhZWBi4gQ7lZAViYAByALOSnJQlAwP3PwYmrn8MBjoxDBrS+kCD/zM8eneAYcGBRIar908xPL1zDxjtP54ABBDz188//hgaR4V/ljzJYiYfzuBvXszAwsQFjgx2qIEcrBBalF+G4S/zBwYdBU8GW41whv//XjP8/f2UgZeLiYFPGJhRWIMY1s/qPfTv+8OFAAEELN14uTR1G1fZNQl6CwpyMJhLRDC8/grJHMCUBCwngMGByP8MnEBfcLBA8M/fLxl4WP8z6EpJMPxj+s+QmDz/+5l1GfnAGJgDEEDMoJT95tXjR3KCYSH6zuzsclwSDFwsbGCNoGDgAmIeIOZlh2KgwXxAzM/BwCDDx8OgIszDICvEwDB9yT2GVRPytjL8fTkFlLUAAghoMNDRDC8f3Tr35IumdISbn6cMkwDQIDZgKSLMgTCQH4gFgXlECCgmwgUMFh6gwbwgw4GGrnvGUJOfffH/l2NNQMMugnwHEECQcghsOJBktqtILj3688mH//9//v///+L7e//vfvj6/9mn//9fAvGbr///fwVK/Pr3Hww+/f7/v3r6jf+swl4XgNr9oYaBAUAAMSLYsJBUjdAxTa8Lz3TQ/Kh3kuETxz0GYS4uBjZgzgOV2zo8+gxaTPYM50/+ZZg4dc3309sm7WL4e20SUHI/WAEUAAQQFoMFgPiLIrAoipTTdvEXVBNS4FZg5mHlZWD5/v33/3cPf/z4evfL++dXj19k+HFyG1DfTqCGh+g1CEAAYTFYHIjfQStUBglgCKsDE68UsJwFhuxfoIuA1QfD+6dAudugyGHAAQACDAAcHoIpsrsiZAAAAABJRU5ErkJggg==" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>

<!-- BEGIN itemblock -->
  <entry>
    <title>{title}</title>
    <id>{baseurl}{bid}.opds</id>
    <content type="text">ISBN: {isbn}</content>
    <link type="application/atom+xml;type=entry;profile=opds-catalog" href="{baseurl}?action=bookdetails&amp;book={bid}&amp;lang={lang}"/>
    <link type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABYAAAAWCAYAAADEtGw7AAAABGdBTUEAAK/INwWK6QAAABl0RVh0U29mdHdhcmUAQWRvYmUgSW1hZ2VSZWFkeXHJZTwAAATbSURBVHjaYvz//z8DDDBZzGVg/POP4R8LKwMrEyMDI8M/BmZODgZGRkaG/x+/Mvzj4tBW15YvenNz14dn+5rrGVi4v4A1/vkKNwNmHkAAMTHgAoxA/Pcfw+9ffxl+ffttpGmpPLe8zPHQ3Ik2SY6WCsEMTOLKDGrxDAwcIli1AwQQC3Yz/zP8/vqLhZmH01xLTzol0E871NZIjJuFGeii3wwM8kpqogyKPhEMUuZGDPfXbgZqeYNuBkAAMSIHBaPZHAaG70CdnBy2+paq+YF+mp7W+sJcbEDXf/7OwPDzDwMDOxsDw8N77/5ffv6LcduND38edQfmMny+MQM9KAACCMXFTGysLpqWmgWhflouFvqC7BxAAz8Bg+/DXwaGf0D1QIrh2w+g73l5GD2YnjN8/cHBsphHwRRo8BxQSCObBRBAKAbrOBv0VqTq60mwAw0DGvgRaNLf/xAMjFMIDVT3j4mZ4eXn/wyKYkCF4hpKDM93CAGFXyGbBRBAKJH38/Wrqx8+MzC8/vKf4RswRL4DTfkGxF+B+PtfCAaJf2NgZvjLysmgIsDAwCOvq8bAwCKHHsYAAYRi8OtLBy4+evsPaAAjxBCoYT+gFoANBVkCdP2Xf8wMwix/GGQUZEQZGLgU0Q0GCCAUgz8/u3X59bufv3/8gxgCMhAUl9+ghoMs+Qk1+PlvVmDK+cmgqirFysApr4puMEAAoRj8+6/CvWf37zz79h9i2DeoS78j0yDxX8DgYWYHWsTMoCnFw8AgpKQO1M6JbBZAAKFmkK8Pnz+5fffWB1DS+gc17C/EwO9/US35CYzAp58ZGVRFmBlYpTU0gbpFkY0CCCBUg/8xfXz/9M3NL5//gb0LC+dvSIbCMQMrw6NvbAzSPCwMfHLK0gwMzDLIRgEEEKrBvx8zfHp2+8LPr98YfvxFM+gPwiJQuP8EpvEXwHBmATpAWkFJCORsZKMAAgjFYHbdUIav/8Wvv37x9s2Pv0jB8AfhcpgFP4Hx8PIPC8O7zz8ZdOUEOBi4ZbWRzQMIINR0/Og8w9/7px5+fP7yHYj/Bcm1P35DXApm/4GmDk4uhpdfGRl05YEJWlARFIECMLMAAgg1KF6dAhYnJ149v//w4T+gq76AkhxS6gCxf4KSJdC1z7/+Z3jy6S/DpYc/GLSkuRlYJZWVkMMZIIBQSzcJO2A4//795N6T8+Y//rj+Anr1HyMoToFZmRWYaICp5dPbrwys798xaLB8ZLAS+cpgpczB8PQXKwOXtLL4RyY+UA68BDIKIIBYMFI1MxPD92cPr/76AiwsePkZ3nwClhkffzD8e3CPQZflHYOr0B8GIWFgscrCxXDlIyvDqk0fGW7dvc3w9fa5TwyMP+HmAQQQC0bxDKw5fvxkvHjp0pPf//nfsTI8ecQQf3gag+KzEwybg5sYdn2Q+n/3/osv3x7ffM3w8todhne3bjF8fXCJ4f/XG0ADrsJMAggg1PJY1BlUoAIZjCyMMvozedUM4gRZmBmXXahjkPhwj9nhq/aRxz+/bmD49foBw7+v94BaHgPxB+QiE2YeQACBGTCM6ngObgY+BXc+NuHUM+JMVx/LMvy3ZWCYC0qVDHgAzCyAAGLBXecxf2X48W7nn1+fmCa9Y/j3nJHB4QIDwyFobUgQAAQYACmANJDUx0lSAAAAAElFTkSuQmCC" rel="http://opds-spec.org/image/thumbnail"/>
    <updated>{pubdate}</updated>
  </entry>
<!-- END itemblock -->

</feed>