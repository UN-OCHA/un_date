# UN Date formatters and twig filters

See [UN standard](https://www.un.org/dgacm/en/content/editorial-manual/numbers-dates-time#dates) for details.

All dates will be outputted as entered, there's no automatic timezone conversion.

We support English, French, Spanish, Chinese and Arabic.

Warning: You need the [patch](https://www.drupal.org/files/issues/2021-12-25/3004425-41.patch)
to fix translation of [abbreviated month names](https://www.drupal.org/node/3004425).

The Humand readable output of RRules is flaky.

## Install

Run `composer install unocha/un_date`

## Core date formatter service

This service is replaced by `UnDateFormatter` and will enforce the proper format for `short`, `medium` and `long`.

## Formatters

There's support for the following

- Core datetime field
- Core datetime_range field
- Contrib [daterange_timezone](https://www.drupal.org/project/datetime_range_timezone)
- Contrib [date_recur](https://www.drupal.org/project/date_recur)

## Twig filters and functions

Input can be either a DateTime compatible object, a Drupal structured data item, a string or a timestamp.

Most filters have an option for month_format, either `numeric`, `full` or `abbreviation` (default numeric)

### Filters

- un_date: Formatted as date `j.m.Y`
- un_time: Formatted as time `g.i a.m.|p.m.`
- un_datetime: Formatted as date and time `d.m.Y g.i a.m.|p.m.`
- un_html_datetime: Formatted as date and time `Y-m-dTH:i:s`
- un_daterange: Formatted as date and time `j.m.Y g.i a.m.|p.m. — g.i a.m.|p.m.` or `j.m.Y g.i a.m.|p.m. — j.m.Y g.i a.m.|p.m.` if dates are different
- un_daterange_times: Formatted as date and time `g.i a.m.|p.m. — g.i a.m.|p.m.` or `j.m.Y g.i a.m.|p.m. — j.m.Y g.i a.m.|p.m.` if dates are different
- un_timerange: Formatted as time `g.i a.m.|p.m. — g.i a.m.|p.m.`
- un_year: Formatted as `Y`
- un_month: Formatted as `m`
- un_month_full: Formatted as `F`
- un_month_abbr: Formatted as `M`
- un_day: Formatted as `j`
- un_hour: Formatted as `G`, `g`, `noon`, `midnight`
- un_minute: Formatted as `m` or blank if zero
- un_ampm: Formatted as `a.m.|p.m.` or blank

### Functions

Most function accept a DateRange (compatible) object or 2 DateTime inputs.

- un_is_same_date: identical
- un_is_same_day: on the same day
- un_is_same_month: in the same month and year
- un_is_same_year: in the same year
- un_is_all_day: all day event
- un_is_utc: using UTC timezone
- un_is_rtl: RTL locale
- un_separator: the separator used
- un_duration: Return duration as human readable string

## Human readable RRUle

Currently using https://github.com/rlanvin/php-rrule but https://github.com/simshaun/recurr looks nicer
