<?php declare(strict_types=1);

namespace cachedvalueforatk\tests;

use Atk4\Data\Persistence;
use atk4\schema\Migration;
use cachedvalueforatk\CachedValue;
use traitsforatkdata\TestCase;

class CachedValueTest extends TestCase
{
    protected $sqlitePersistenceModels = [CachedValue::class];

    public function testExistingSettingUpdated()
    {
        $persistence = $this->getSqliteTestPersistence();
        $initial_count = (new CachedValue($persistence))->action('count')->getOne();
        $cachedValue = new CachedValue($persistence);
        $cachedValue->set('ident', 'LALA');
        $cachedValue->set('value', '1');
        $cachedValue->save();

        $cachedValue = new CachedValue($persistence);
        $cachedValue->set('ident', 'LALA');
        $cachedValue->set('value', '2');
        $cachedValue->save();

        self::assertEquals($initial_count + 1, (new CachedValue($persistence))->action('count')->getOne());
    }

    public function testLastUpdatedIsUpdated()
    {
        $persistence = $this->getSqliteTestPersistence();
        $cachedValue = new CachedValue($persistence);
        $cachedValue->set('ident', 'LALA');
        $cachedValue->set('value', '1');
        $cachedValue->save();
        $cachedValue->set('value', '2');
        $cachedValue->save();
        $lastUpdated = $cachedValue->get('last_updated');
        sleep(1);
        $cachedValue->set('value', '3');
        $cachedValue->save();
        $newLastUpdated = $cachedValue->get('last_updated');
        self::assertNotSame(
            $lastUpdated,
            $newLastUpdated
        );
    }
}
