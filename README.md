# UN Date formatters and twig filters

Standard at https://www.un.org/dgacm/en/content/editorial-manual/numbers-dates-time#dates

## Todo

- Test twig filter with full name
- Test formatters with full name
- Add translations

## Formatters

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

Works on both `\Drupal\date_recur\Plugin\Field\FieldType\DateRecurFieldItemList` and `Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem`

## un_daterange_times

Formatted as date and time `h.mm a.m.|p.m. — h.mm a.m.|p.m.`
or `dd.mm.yyyy h.mm a.m.|p.m. — dd.mm.yyyy h.mm a.m.|p.m.` if dates are different

Works on both `\Drupal\date_recur\Plugin\Field\FieldType\DateRecurFieldItemList` and `Drupal\date_recur\Plugin\Field\FieldType\DateRecurItem`

## Examples

```twig
<pre>
Parameters:
- show_timezone to display the timezone (default false)
- month_format: Either `numeric`, `full` or `abbreviation` (default numeric)

node.field_date|un_daterange_times(true, 'numeric'): {{ node.field_date|un_daterange_times(true, 'numeric') }}
node.field_date|un_daterange_times(true, 'numeric'): {{ node.field_date|un_daterange_times(true, 'numeric') }}
node.field_date|un_daterange_times(false, 'numeric'): {{ node.field_date|un_daterange_times(false, 'numeric') }}
node.field_date|un_daterange_times(false, 'numeric'): {{ node.field_date|un_daterange_times(false, 'numeric') }}
node.field_date|un_daterange_times: {{ node.field_date|un_daterange_times }}

node.field_date|un_daterange(true, 'numeric'): {{ node.field_date|un_daterange(true, 'numeric') }}
node.field_date|un_daterange(true, 'numeric'): {{ node.field_date|un_daterange(true, 'numeric') }}
node.field_date|un_daterange(false, 'numeric'): {{ node.field_date|un_daterange(false, 'numeric') }}
node.field_date|un_daterange(false, 'numeric'): {{ node.field_date|un_daterange(false, 'numeric') }}
node.field_date|un_daterange: {{ node.field_date|un_daterange }}

node.field_date|un_daterange_named('local_times'): {{ node.field_date|un_daterange_named('local_times') }}

un_is_all_day(node.field_date): {{ un_is_all_day(node.field_date) }}

node.field_date[0].start_date|un_time(true, 'numeric'): {{ node.field_date[0].start_date|un_time(true, 'numeric') }}
node.field_date[0].start_date|un_time(true, 'numeric'): {{ node.field_date[0].start_date|un_time(true, 'numeric') }}
node.field_date[0].start_date|un_time(false, 'numeric'): {{ node.field_date[0].start_date|un_time(false, 'numeric') }}
node.field_date[0].start_date|un_time(false, 'numeric'): {{ node.field_date[0].start_date|un_time(false, 'numeric') }}
node.field_date[0].start_date|un_time: {{ node.field_date[0].start_date|un_time }}

node.field_date[0].start_date|un_date(true, 'numeric'): {{ node.field_date[0].start_date|un_date(true, 'numeric') }}
node.field_date[0].start_date|un_date(true, 'numeric'): {{ node.field_date[0].start_date|un_date(true, 'numeric') }}
node.field_date[0].start_date|un_date(false, 'numeric'): {{ node.field_date[0].start_date|un_date(false, 'numeric') }}
node.field_date[0].start_date|un_date(false, 'numeric'): {{ node.field_date[0].start_date|un_date(false, 'numeric') }}
node.field_date[0].start_date|un_date: {{ node.field_date[0].start_date|un_date }}

node.field_date[0].start_date|un_datetime(true, 'numeric'): {{ node.field_date[0].start_date|un_datetime(true, 'numeric') }}
node.field_date[0].start_date|un_datetime(true, 'numeric'): {{ node.field_date[0].start_date|un_datetime(true, 'numeric') }}
node.field_date[0].start_date|un_datetime(false, 'numeric'): {{ node.field_date[0].start_date|un_datetime(false, 'numeric') }}
node.field_date[0].start_date|un_datetime(false, 'numeric'): {{ node.field_date[0].start_date|un_datetime(false, 'numeric') }}
node.field_date[0].start_date|un_datetime: {{ node.field_date[0].start_date|un_datetime }}
</pre>
```

Output

```
Parameters:
- show_timezone to display the timezone (default false)

node.field_date|un_daterange_times(true, 'numeric'): 3.30 p.m. — 5.00 p.m. UTC
node.field_date|un_daterange_times(true, 'numeric'): 3.30 p.m. — 5.00 p.m.
node.field_date|un_daterange_times(false, 'numeric'): 5.30 p.m. — 7.00 p.m. Europe/Brussels
node.field_date|un_daterange_times(false, 'numeric'): 5.30 p.m. — 7.00 p.m.
node.field_date|un_daterange_times: 5.30 p.m. — 7.00 p.m.

node.field_date|un_daterange(true, 'numeric'): 17.05.2021 3.30 p.m. — 5.00 p.m. UTC
node.field_date|un_daterange(true, 'numeric'): 17.05.2021 3.30 p.m. — 5.00 p.m.
node.field_date|un_daterange(false, 'numeric'): 17.05.2021 5.30 p.m. — 7.00 p.m. Europe/Brussels
node.field_date|un_daterange(false, 'numeric'): 17.05.2021 5.30 p.m. — 7.00 p.m.
node.field_date|un_daterange: 17.05.2021 5.30 p.m. — 7.00 p.m.

node.field_date|un_daterange_named('local_times'): 5.30 p.m. — 7.00 p.m. Europe/Brussels

un_is_all_day(node.field_date):

node.field_date[0].start_date|un_time(true, 'numeric'): 3.30 p.m. UTC
node.field_date[0].start_date|un_time(true, 'numeric'): 3.30 p.m.
node.field_date[0].start_date|un_time(false, 'numeric'): 5.30 p.m. Europe/Brussels
node.field_date[0].start_date|un_time(false, 'numeric'): 5.30 p.m.
node.field_date[0].start_date|un_time: 5.30 p.m.

node.field_date[0].start_date|un_date(true, 'numeric'): 17.05.2021
node.field_date[0].start_date|un_date(true, 'numeric'): 17.05.2021
node.field_date[0].start_date|un_date(false, 'numeric'): 17.05.2021
node.field_date[0].start_date|un_date(false, 'numeric'): 17.05.2021
node.field_date[0].start_date|un_date: 17.05.2021

node.field_date[0].start_date|un_datetime(true, 'numeric'): 17.05.2021 3.30 p.m. UTC
node.field_date[0].start_date|un_datetime(true, 'numeric'): 17.05.2021 3.30 p.m.
node.field_date[0].start_date|un_datetime(false, 'numeric'): 17.05.2021 5.30 p.m. Europe/Brussels
node.field_date[0].start_date|un_datetime(false, 'numeric'): 17.05.2021 5.30 p.m.
node.field_date[0].start_date|un_datetime: 17.05.2021 5.30 p.m.
```
