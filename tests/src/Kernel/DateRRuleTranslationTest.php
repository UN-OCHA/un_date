<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\Core\StringTranslation\StringTranslationTrait;
use Drupal\un_date\Trait\UnDateTimeTrait;
use Drupal\un_date\UnRRuleHumanReadable;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateRRuleTranslationTest extends UnDateTestBase {

  use UnDateTimeTrait;
  use StringTranslationTrait;

  /**
   * {@inheritdoc}
   */
  protected static $modules = [
    'locale',
    'language',
    'system',
    'datetime',
    'un_date',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    $this->testConfig = [
      'timezone' => 'UTC',
      'storage' => [
        'type' => 'datetime',
        'settings' => [],
      ],
      'display' => [
        'type' => 'un_date_datetime',
        'settings' => [
          'display_timezone' => FALSE,
        ],
      ],
    ];

    parent::setUp();
  }

  /**
   * Test RRule in English.
   *
   * @x-dataProvider providerTestData
   */
  public function testRruleEnglish($rule = NULL, $result = NULL) {
    $lang_code = 'en';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerTestData(),
      );

      foreach ($data as $name => $row) {
        $rule = $row['rule'];
        $result = $row['result'];

        $parser = new UnRRuleHumanReadable($rule);
        $output = $parser->humanReadable([
          'use_intl' => TRUE,
          'locale' => $lang_code,
          'explicit_inifite' => TRUE,
          'dtstart' => FALSE,
          'include_start' => FALSE,
          'include_until' => TRUE,
          'date_formatter' => function ($date) {
            return $this->formatDate($date, 'numeric');
          },
        ]);

        self::assertSame($output, $result[$lang_code], $name);
      }
    }
    else {
      $parser = new UnRRuleHumanReadable($rule);
      $output = $parser->humanReadable([
        'use_intl' => TRUE,
        'locale' => $lang_code,
        'explicit_inifite' => TRUE,
        'dtstart' => FALSE,
        'include_start' => FALSE,
        'include_until' => TRUE,
        'date_formatter' => function ($date) {
          return $this->formatDate($date, 'numeric');
        },
      ]);

      self::assertSame($output, $result[$lang_code]);
    }
  }

  /**
   * Test RRule in French.
   *
   * @x-dataProvider providerTestData
   */
  public function testRruleFrench($rule = NULL, $result = NULL) {
    $lang_code = 'en';

    $this->importTranslations();
    $this->setLanguage($lang_code);

    if ($this->inlineDataProvider) {
      $data = array_merge(
        $this->providerTestData(),
      );

      foreach ($data as $name => $row) {
        $rule = $row['rule'];
        $result = $row['result'];

        $parser = new UnRRuleHumanReadable($rule);
        $output = $parser->humanReadable([
          'use_intl' => TRUE,
          'locale' => $lang_code,
          'explicit_inifite' => TRUE,
          'dtstart' => FALSE,
          'include_start' => FALSE,
          'include_until' => TRUE,
          'date_formatter' => function ($date) {
            return $this->formatDate($date, 'numeric');
          },
        ]);

        self::assertSame($output, $result[$lang_code], $name);
      }
    }
    else {
      $parser = new UnRRuleHumanReadable($rule);
      $output = $parser->humanReadable([
        'use_intl' => TRUE,
        'locale' => $lang_code,
        'explicit_inifite' => TRUE,
        'dtstart' => FALSE,
        'include_start' => FALSE,
        'include_until' => TRUE,
        'date_formatter' => function ($date) {
          return $this->formatDate($date, 'numeric');
        },
      ]);

      self::assertSame($output, $result[$lang_code]);
    }
  }

  /**
   * Provide test examples.
   */
  public function providerTestData() {
    return [
      __FUNCTION__ . '::FREQ=DAILY;UNTIL=20000131T140000Z;BYMONTH=1' => [
        'rule' => 'FREQ=DAILY;UNTIL=20000131T140000Z;BYMONTH=1',
        'result' => [
          'en' => 'Daily in January, until 31.01.2000',
          'fr' => 'Tous les jours en janvier, jusqu\'au 31.01.2000',
        ],
      ],
      __FUNCTION__ . '::FREQ=YEARLY;INTERVAL=3;COUNT=10;BYYEARDAY=1,100,200' => [
        'rule' => 'FREQ=YEARLY;INTERVAL=3;COUNT=10;BYYEARDAY=1,100,200',
        'result' => [
          'en' => 'Every 3 years on the first, the 100th and the 200th days of the year, 10 times',
          'fr' => 'Tous les 3 ans les 1er, 100e et 200e jours de l\'année, 10 fois',
        ],
      ],
      __FUNCTION__ . '::FREQ=YEARLY;BYWEEKNO=20;BYDAY=MO' => [
        'rule' => 'FREQ=YEARLY;BYWEEKNO=20;BYDAY=MO',
        'result' => [
          'en' => 'Yearly on Monday on week 20, indefinitely',
          'fr' => 'Tous les ans le lundi la semaine 20, indéfiniment',
        ],
      ],
      __FUNCTION__ . '::FREQ=MONTHLY;BYDAY=FR;BYMONTHDAY=13' => [
        'rule' => 'FREQ=MONTHLY;BYDAY=FR;BYMONTHDAY=13',
        'result' => [
          'en' => 'Monthly on Friday on the 13th of the month, indefinitely',
          'fr' => 'Tous les mois le vendredi le 13 du mois, indéfiniment',
        ],
      ],
      __FUNCTION__ . '::FREQ=MONTHLY;BYDAY=TU' => [
        'rule' => 'FREQ=MONTHLY;BYDAY=TU',
        'result' => [
          'en' => 'Monthly on Tuesday, indefinitely',
          'fr' => 'Tous les mois le mardi, indéfiniment',
        ],
      ],
      __FUNCTION__ . '::FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=1' => [
        'rule' => 'FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=1',
        'result' => [
          'en' => 'Monthly on the first Tuesday, indefinitely',
          'fr' => 'Tous les mois, mais seulement le premier mardi, indéfiniment',
        ],
      ],
      __FUNCTION__ . '::FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=-1' => [
        'rule' => 'FREQ=MONTHLY;INTERVAL=1;BYDAY=TU;BYSETPOS=-1',
        'result' => [
          'en' => 'Monthly on the last Tuesday, indefinitely',
          'fr' => 'Tous les mois, mais seulement le dernier mardi, indéfiniment',
        ],
      ],
      __FUNCTION__ . '::FREQ=DAILY;INTERVAL=1;COUNT=3' => [
        'rule' => 'FREQ=DAILY;INTERVAL=1;COUNT=3',
        'result' => [
          'en' => 'Daily, 3 times',
          'fr' => 'Tous les jours, 3 fois',
        ],
      ],
      __FUNCTION__ . '::FREQ=DAILY;INTERVAL=1;COUNT=1' => [
        'rule' => 'FREQ=DAILY;INTERVAL=1;COUNT=1',
        'result' => [
          'en' => '',
          'fr' => '',
        ],
      ],
      __FUNCTION__ . '::FREQ=WEEKLY;INTERVAL=1;COUNT=1' => [
        'rule' => 'FREQ=WEEKLY;INTERVAL=1;COUNT=1',
        'result' => [
          'en' => '',
          'fr' => '',
        ],
      ],
      __FUNCTION__ . '::FREQ=WEEKLY;INTERVAL=1;BYDAY=WE;COUNT=1' => [
        'rule' => 'FREQ=WEEKLY;INTERVAL=1;BYDAY=WE;COUNT=1',
        'result' => [
          'en' => 'Weekly on Wednesday, one time',
          'fr' => 'Toutes les semaines le mercredi, une fois',
        ],
      ],
      __FUNCTION__ . '::FREQ=WEEKLY;INTERVAL=1;BYDAY=WE,SA;COUNT=1' => [
        'rule' => 'FREQ=WEEKLY;INTERVAL=1;BYDAY=WE,SA;COUNT=1',
        'result' => [
          'en' => 'Weekly on Wednesday and Saturday, one time',
          'fr' => 'Toutes les semaines les mercredi et samedi, une fois',
        ],
      ],
      __FUNCTION__ . '::FREQ=WEEKLY;INTERVAL=2;BYDAY=WE,SA;UNTIL=20220831T000000Z' => [
        'rule' => 'FREQ=WEEKLY;INTERVAL=2;BYDAY=WE,SA;UNTIL=20220831T000000Z',
        'result' => [
          'en' => 'Every other week on Wednesday and Saturday, until 31.08.2022',
          'fr' => 'Une semaine sur deux les mercredi et samedi, jusqu\'au 31.08.2022',
        ],
      ],
    ];
  }

}
