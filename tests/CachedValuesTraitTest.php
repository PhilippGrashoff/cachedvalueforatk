<?php declare(strict_types=1);

namespace cachedvalueforatk\tests;

use traitsforatkdata\TestCase;
use Atk4\Data\Persistence;
use atk4\schema\Migration;
use Atk4\Ui\App;
use cachedvalueforatk\CachedValue;
use cachedvalueforatk\CachedValuesTrait;


class CachedValuesTraitTest extends TestCase
{

    protected $sqlitePersistenceModels = [CachedValue::class];

    public function testgetCachedValue()
    {
        $app = $this->getAppWithCachedValue();
        $app->setCachedValue('LALA', 'hamma');
        self::assertEquals(
            'hamma',
            $app->getCachedValue(
                'LALA',
                function () {
                    return 'Duggu';
                }
            )
        );
    }

    public function testgetCachedValueWithTimeout()
    {
        $app = $this->getAppWithCachedValue();
        $app->setCachedValue('DADA', 'hamma');
        self::assertEquals(
            'hamma',
            $app->getCachedValue(
                'DADA',
                function () {
                    return 'Duggu';
                },
                1
            )
        );
        usleep(1500000);
        self::assertEquals(
            'Duggu',
            $app->getCachedValue(
                'DADA',
                function () {
                    return 'Duggu';
                },
                1
            )
        );
    }

    public function testgetNonExistantCachedValue()
    {
        $app = $this->getAppWithCachedValue();
        self::assertEquals(
            'hamma',
            $app->getCachedValue(
                'HAKIRILI',
                function () {
                    return 'hamma';
                }
            )
        );
    }

    public function testSetCachedValueTwiceDoesNotCauseException()
    {
        $app = $this->getAppWithCachedValue();
        self::assertEquals(
            'hamma',
            $app->getCachedValue(
                'HAKIRILI',
                function () {
                    return 'hamma';
                }
            )
        );
        $app->setCachedValue('HAKIRILI', 'Mausi');
        self::assertEquals(
            'Mausi',
            $app->getCachedValue(
                'HAKIRILI',
                function () {
                    return 'hamma';
                }
            )
        );
    }

    protected function getAppWithCachedValue(): App {
        $class = new class() extends App {
            use CachedValuesTrait;

            public $always_run = false;
        };

        $persistence = $this->getSqliteTestPersistence();

        $instance = new $class();
        $instance->db = $persistence;

        return $instance;
    }
}