# UN Date twig filters

## Todo

- Add support for *all day*

All filter allow options

- to_utc to convert date to UTC first (default off), otherwise use user/site timezone
- show_timezone to display the timezone (default off)

## un_date

Formatted as date `dd.mm.yyyy`

## un_time

Formatted as time `h.mm a.m.|p.m.`

## un_datetime

Formatted as date and time `dd.mm.yyyy h.mm a.m.|p.m.`

## un_daterange

Formatted as date and time `dd.mm.yyyy h.mm a.m.|p.m. — h.mm a.m.|p.m.`
or `dd.mm.yyyy h.mm a.m.|p.m. — dd.mm.yyyy h.mm a.m.|p.m.` if dates are different

Works on both `\Drupal\date_recur\Plugin\Field\FieldType\DateRecurFieldItemList` and `Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem`

## un_daterange_times

Formatted as date and time `h.mm a.m.|p.m. — h.mm a.m.|p.m.`
or `dd.mm.yyyy h.mm a.m.|p.m. — dd.mm.yyyy h.mm a.m.|p.m.` if dates are different

Works on both `\Drupal\date_recur\Plugin\Field\FieldType\DateRecurFieldItemList` and `Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem`

## Examples

```twig
<pre>
Parameters:
- to_utc to convert date to UTC first (default false)
- show_timezone to display the timezone (default false)

node.field_date|un_daterange_times(true, true): {{ node.field_date|un_daterange_times(true, true) }}
node.field_date|un_daterange_times(true, false): {{ node.field_date|un_daterange_times(true, false) }}
node.field_date|un_daterange_times(false, true): {{ node.field_date|un_daterange_times(false, true) }}
node.field_date|un_daterange_times(false, false): {{ node.field_date|un_daterange_times(false, false) }}
node.field_date|un_daterange_times: {{ node.field_date|un_daterange_times }}

node.field_date|un_daterange(true, true): {{ node.field_date|un_daterange(true, true) }}
node.field_date|un_daterange(true, false): {{ node.field_date|un_daterange(true, false) }}
node.field_date|un_daterange(false, true): {{ node.field_date|un_daterange(false, true) }}
node.field_date|un_daterange(false, false): {{ node.field_date|un_daterange(false, false) }}
node.field_date|un_daterange: {{ node.field_date|un_daterange }}

node.field_date|un_daterange_named('local_times'): {{ node.field_date|un_daterange_named('local_times') }}

un_is_all_day(node.field_date): {{ un_is_all_day(node.field_date) }}

node.field_date[0].start_date|un_time(true, true): {{ node.field_date[0].start_date|un_time(true, true) }}
node.field_date[0].start_date|un_time(true, false): {{ node.field_date[0].start_date|un_time(true, false) }}
node.field_date[0].start_date|un_time(false, true): {{ node.field_date[0].start_date|un_time(false, true) }}
node.field_date[0].start_date|un_time(false, false): {{ node.field_date[0].start_date|un_time(false, false) }}
node.field_date[0].start_date|un_time: {{ node.field_date[0].start_date|un_time }}

node.field_date[0].start_date|un_date(true, true): {{ node.field_date[0].start_date|un_date(true, true) }}
node.field_date[0].start_date|un_date(true, false): {{ node.field_date[0].start_date|un_date(true, false) }}
node.field_date[0].start_date|un_date(false, true): {{ node.field_date[0].start_date|un_date(false, true) }}
node.field_date[0].start_date|un_date(false, false): {{ node.field_date[0].start_date|un_date(false, false) }}
node.field_date[0].start_date|un_date: {{ node.field_date[0].start_date|un_date }}

node.field_date[0].start_date|un_datetime(true, true): {{ node.field_date[0].start_date|un_datetime(true, true) }}
node.field_date[0].start_date|un_datetime(true, false): {{ node.field_date[0].start_date|un_datetime(true, false) }}
node.field_date[0].start_date|un_datetime(false, true): {{ node.field_date[0].start_date|un_datetime(false, true) }}
node.field_date[0].start_date|un_datetime(false, false): {{ node.field_date[0].start_date|un_datetime(false, false) }}
node.field_date[0].start_date|un_datetime: {{ node.field_date[0].start_date|un_datetime }}
</pre>
```

Output

```
Parameters:
- to_utc to convert date to UTC first (default false)
- show_timezone to display the timezone (default false)

node.field_date|un_daterange_times(true, true): 3.30 p.m. — 5.00 p.m. UTC
node.field_date|un_daterange_times(true, false): 3.30 p.m. — 5.00 p.m.
node.field_date|un_daterange_times(false, true): 5.30 p.m. — 7.00 p.m. Europe/Brussels
node.field_date|un_daterange_times(false, false): 5.30 p.m. — 7.00 p.m.
node.field_date|un_daterange_times: 5.30 p.m. — 7.00 p.m.

node.field_date|un_daterange(true, true): 17.05.2021 3.30 p.m. — 5.00 p.m. UTC
node.field_date|un_daterange(true, false): 17.05.2021 3.30 p.m. — 5.00 p.m.
node.field_date|un_daterange(false, true): 17.05.2021 5.30 p.m. — 7.00 p.m. Europe/Brussels
node.field_date|un_daterange(false, false): 17.05.2021 5.30 p.m. — 7.00 p.m.
node.field_date|un_daterange: 17.05.2021 5.30 p.m. — 7.00 p.m.

node.field_date|un_daterange_named('local_times'): 5.30 p.m. — 7.00 p.m. Europe/Brussels

un_is_all_day(node.field_date):

node.field_date[0].start_date|un_time(true, true): 3.30 p.m. UTC
node.field_date[0].start_date|un_time(true, false): 3.30 p.m.
node.field_date[0].start_date|un_time(false, true): 5.30 p.m. Europe/Brussels
node.field_date[0].start_date|un_time(false, false): 5.30 p.m.
node.field_date[0].start_date|un_time: 5.30 p.m.

node.field_date[0].start_date|un_date(true, true): 17.05.2021
node.field_date[0].start_date|un_date(true, false): 17.05.2021
node.field_date[0].start_date|un_date(false, true): 17.05.2021
node.field_date[0].start_date|un_date(false, false): 17.05.2021
node.field_date[0].start_date|un_date: 17.05.2021

node.field_date[0].start_date|un_datetime(true, true): 17.05.2021 3.30 p.m. UTC
node.field_date[0].start_date|un_datetime(true, false): 17.05.2021 3.30 p.m.
node.field_date[0].start_date|un_datetime(false, true): 17.05.2021 5.30 p.m. Europe/Brussels
node.field_date[0].start_date|un_datetime(false, false): 17.05.2021 5.30 p.m.
node.field_date[0].start_date|un_datetime: 17.05.2021 5.30 p.m.
```
