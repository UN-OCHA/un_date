<?php

/**
 * @file
 * Translation file for Spanish language.
 */

return [
  'on' => ' on ',
  'yearly' => [
    '1' => 'anual',
// Cada 8 años.
    'else' => 'cada %{interval} años',
  ],
  'monthly' => [
    '1' => 'mensual',
  // Cada 8 meses.
    'else' => 'cada %{interval} meses',
  ],
  'weekly' => [
    '1' => 'semanal',
    '2' => 'cualquier otra semana',
  // Cada 8 semanas.
    'else' => 'cada %{interval} semanas',
  ],
  'daily' => [
    '1' => 'diario',
    '2' => 'cualquier otro día',
  // Cada 8 días.
    'else' => 'cada %{interval} días',
  ],
  'hourly' => [
    '1' => 'cada hora',
  // Cada 8 horas.
    'else' => 'cada %{interval} horas',
  ],
  'minutely' => [
    '1' => 'cada minuto',
  // Cada 8 minutos.
    'else' => 'cada %{interval} minutos',
  ],
  'secondly' => [
    '1' => 'segundo lugar',
  // Cada 8 segundos.
    'else' => 'cada %{interval} segundos',
  ],
  'dtstart' => ', empezando desde %{date}',
  'infinite' => ', por siempre',
  'until' => ', hasta %{date}',
  'count' => [
    '1' => ', una vez',
    'else' => ', %{count} veces',
  ],
  'and' => 'y ',
  'x_of_the_y' => [
  // e.g. the first Monday of the year, or the first day of the year.
    'yearly' => '%{x} del año',
    'monthly' => '%{x} del mes',
  ],
  'bymonth' => ' en %{months}',
  'months' => [
    1 => 'Enero',
    2 => 'Febrero',
    3 => 'Marzo',
    4 => 'Abril',
    5 => 'Mayo',
    6 => 'Junio',
    7 => 'Julio',
    8 => 'Agosto',
    9 => 'Septiembre',
    10 => 'Octubre',
    11 => 'Noviembre',
    12 => 'Diciembre',
  ],
  'byweekday' => ' en %{weekdays}',
  'byweekday_without' => '%{weekdays}',
  'weekdays' => [
    1 => 'Lunes',
    2 => 'Martes',
    3 => 'Miércoles',
    4 => 'Jueves',
    5 => 'Viernes',
    6 => 'Sábado',
    7 => 'Domingo',
  ],
  'nth_weekday' => [
  // e.g. the first Monday.
    '1' => 'El primer %{weekday}',
    '2' => 'El segundo %{weekday}',
    '3' => 'El tercero %{weekday}',
    'else' => 'El %{n}° %{weekday}',
  ],
  '-nth_weekday' => [
  // e.g. the last Monday.
    '-1' => 'El último %{weekday}',
    '-2' => 'El penúltimo %{weekday}',
    '-3' => 'El antepenúltimo %{weekday}',
    'else' => 'El %{n}° hasta el último	 %{weekday}',
  ],
  'byweekno' => [
    '1' => ' en semana %{weeks}',
    'else' => ' en semana # %{weeks}',
  ],
  'nth_weekno' => '%{n}',
  'bymonthday' => ' en %{monthdays}',
  'nth_monthday' => [
    '1' => 'El 1°',
    '2' => 'El 2°',
    '3' => 'El 3°',
    '21' => 'El 21°',
    '22' => 'El 22°',
    '23' => 'El 23°',
    '31' => 'El 31°',
    'else' => 'El %{n}°',
  ],
  '-nth_monthday' => [
    '-1' => 'El último día',
    '-2' => 'El penúltimo día',
    '-3' => 'El antepenúltimo día',
    '-21' => 'El 21° hasta el último día',
    '-22' => 'El 22° hasta el último día',
    '-23' => 'El 23° hasta el último día',
    '-31' => 'El 31° hasta el último día',
    'else' => 'El %{n}° hasta el último día',
  ],
  'byyearday' => [
    '1' => ' en %{yeardays} día',
    'else' => ' en %{yeardays} días',
  ],
  'nth_yearday' => [
    '1' => 'El primero',
    '2' => 'El segundo',
    '3' => 'El tercero',
    'else' => 'El %{n}°',
  ],
  '-nth_yearday' => [
    '-1' => 'El último',
    '-2' => 'El penúltimo',
    '-3' => 'El antepenúltimo',
    'else' => 'El %{n}° hasta el último',
  ],
  'byhour' => [
    '1' => ' a %{hours}',
    'else' => ' a %{hours}',
  ],
  'nth_hour' => '%{n}h',
  'byminute' => [
    '1' => ' a minutos %{minutes}',
    'else' => ' a minutos %{minutes}',
  ],
  'nth_minute' => '%{n}',
  'bysecond' => [
    '1' => ' a segundo %{seconds}',
    'else' => ' a segundo %{seconds}',
  ],
  'nth_second' => '%{n}',
  'bysetpos' => ', pero solo %{setpos} instancia de este conjunto',
  'nth_setpos' => [
    '1' => 'El primer',
    '2' => 'El segundo',
    '3' => 'El tercero',
    'else' => 'El %{n}°',
  ],
  '-nth_setpos' => [
    '-1' => 'El último',
    '-2' => 'El penúltimo',
    '-3' => 'El antepenúltimo',
    'else' => 'El %{n}° hasta el último',
  ],
];
