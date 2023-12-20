<?php

namespace Drupal\Tests\un_date\Kernel;

use Drupal\datetime\Plugin\Field\FieldType\DateTimeItem;
use Drupal\entity_test\Entity\EntityTest;

/**
 * Test datetime range field type via API.
 *
 * @group datetime
 */
class DateRenderDateTimeFullTranslationTest extends UnDateTestBase {

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
      'storage' => [
        'type' => 'datetime',
        'settings' => [
          'datetime_type' => DateTimeItem::DATETIME_TYPE_DATETIME,
        ],
      ],
      'display' => [
        'type' => 'un_date_datetime',
        'settings' => [
          'display_timezone' => FALSE,
          'month_format' => 'full',
        ]
      ],
    ];
    
    parent::setUp();
  }

  /**
   * Test datetimes in English.
   *
   * @x-dataProvider providerTestData
   */
  public function testDateTime($expected = NULL, $date = NULL) {
    $lang_codes = [
      'en',
      'fr',
      'es',
      'ar',
      'zh-hans',
    ];

    foreach ($lang_codes as $lang_code) {
      $this->importTranslations();
      $this->setLanguage($lang_code);

      if ($this->inlineDataProvider) {
        $data = array_merge(
          $this->providerTestData(),
        );

        foreach ($data as $name => $row) {
          $expected = $row['expected'];
          $date = $row['date'];

          $field_name = $this->fieldStorage->getName();
          // Create an entity.
          $entity = EntityTest::create([
            'name' => $this->randomString(),
            $field_name => [
              'value' => $date,
            ],
          ]);

          $this->assertStringContainsString($expected[$lang_code], (string) $this->renderIt('entity_test', $entity, $lang_code), $name);
        }
      }
      else {
        $field_name = $this->fieldStorage->getName();
        // Create an entity.
        $entity = EntityTest::create([
          'name' => $this->randomString(),
          $field_name => [
            'value' => $date,
          ],
        ]);

        $this->assertStringContainsString($expected[$lang_code], (string) $this->renderIt('entity_test', $entity, $lang_code));
      }
    }
  }

  /**
   * Provide test examples.
   */
  public function providerTestData() {
    return [
      'same' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 10.11 a.m.',
          'fr' => 'Date: 6 décembre 2023 10 h 11',
          'es' => 'Date: 6 diciembre 2023 10.11 horas',
          'ar' => implode('', ['Date: 6 ديسمبر 2023', ' 10.11']),
          'zh-hans' => 'Date: 6 十二月 2023 10時11分',
        ],
        'date' => '2023-12-06T10:11:12',
      ],
      'same_day' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 10.11 p.m.',
          'fr' => 'Date: 6 décembre 2023 22 h 11',
          'es' => 'Date: 6 diciembre 2023 22.11 horas',
          'ar' => implode('', ['Date: 6 ديسمبر 2023', ' 22.11']),
          'zh-hans' => 'Date: 6 十二月 2023 22時11分',
        ],
        'date' => '2023-12-06T22:11:12',
      ],
      '1am' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 1 a.m.',
          'fr' => 'Date: 6 décembre 2023 1 heure',
          'es' => 'Date: 6 diciembre 2023 1 hora',
          'ar' => implode('', ['Date: 6 ديسمبر 2023', ' 1']),
          'zh-hans' => 'Date: 6 十二月 2023 1時正',
        ],
        'date' => '2023-12-06T01:00:00',
      ],
      'next_day' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 10 a.m.',
          'fr' => 'Date: 6 décembre 2023 10 heures',
          'es' => 'Date: 6 diciembre 2023 10 horas',
          'ar' => implode('', ['Date: 6 ديسمبر 2023', ' 10']),
          'zh-hans' => 'Date: 6 十二月 2023 10時正',
        ],
        'date' => '2023-12-06T10:00:12',
      ],
      'all_day' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 midnight',
          'fr' => 'Date: 6 décembre 2023 minuit',
          'es' => 'Date: 6 diciembre 2023 medianoche',
          'ar' => implode('', ['Date: 6 ديسمبر 2023', ' منتصف الليل']),
          'zh-hans' => 'Date: 6 十二月 2023 午夜',
        ],
        'date' => '2023-12-06T00:00:00',
      ],
      'all_day_multi' => [
        'expected' => [
          'en' => 'Date: 6 December 2023 noon',
          'fr' => 'Date: 6 décembre 2023 midi',
          'es' => 'Date: 6 diciembre 2023 mediodía',
          'ar' => implode('', ['Date: 6 ديسمبر 2023', ' وقت الظهيرة']),
          'zh-hans' => 'Date: 6 十二月 2023 中午',
        ],
        'date' => '2023-12-06T12:00:00',
      ],
    ];
  }

}
