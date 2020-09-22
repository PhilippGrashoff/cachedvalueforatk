<?php declare(strict_types=1);

namespace cachedvalueforatk\tests;

use atk4\data\Persistence;
use cachedvalueforatk\CachedValue;
use atk4\core\AtkPhpunit\TestCase;
use atk4\schema\Migration;

class CachedValueTest extends TestCase {

    public function testExistingSettingUpdated() {
        $persistence = $this->getPersistence();
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

    public function testLastUpdatedIsUpdated() {
        $persistence = $this->getPersistence();
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

    protected function getPersistence(): Persistence {
        $persistence = Persistence::connect('sqlite::memory:');
        $model1 = new CachedValue($persistence);
        Migration::of($model1)->drop()->create();

        return $persistence;
    }
}
