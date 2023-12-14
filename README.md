# UN Date formatters and twig filters

See [UN standard](https://www.un.org/dgacm/en/content/editorial-manual/numbers-dates-time#dates) for details.

All dates will be outputted as entered, there's no automatic timezone conversion.

We support English, French, Spanish, Chinese and Arabic.

Warning: You need the [patch](https://www.drupal.org/files/issues/2021-12-25/3004425-41.patch)
to fix translation of [abbreviated month names](https://www.drupal.org/node/3004425).

The Humand readable output of RRules is flaky.

## Install

Run `composer install unocha/un_date`

## Todo

- Test interpreter

## Formatters

There's support for the followig

- Core datetime field
- Core datetime_range field
- Contrib [daterange_timezone](https://www.drupal.org/project/datetime_range_timezone)
- Contrib [date_recur](https://www.drupal.org/project/date_recur)

## Twig filters and functions

All filter allow options

- show_timezone to display the timezone (default off)
- month_format: Either `numeric`, `full` or `abbreviation` (default numeric)

## un_date

Formatted as date `dd.mm.yyyy`

## un_time

Formatted as time `h.mm a.m.|p.m.`

## un_datetime

Formatted as date and time `dd.mm.yyyy h.mm a.m.|p.m.`

## un_daterange

Formatted as date and time `dd.mm.yyyy h.mm a.m.|p.m. — h.mm a.m.|p.m.`
or `dd.mm.yyyy h.mm a.m.|p.m. — dd.mm.yyyy h.mm a.m.|p.m.` if dates are different

## un_daterange_times

Formatted as date and time `h.mm a.m.|p.m. — h.mm a.m.|p.m.`
or `dd.mm.yyyy h.mm a.m.|p.m. — dd.mm.yyyy h.mm a.m.|p.m.` if dates are different
