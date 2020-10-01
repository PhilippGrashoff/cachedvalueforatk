<?php declare(strict_types=1);

namespace cachedvalueforatk;

use atk4\data\Model;
use traitsforatkdata\CreatedDateAndLastUpdatedTrait;


class CachedValue extends Model {

    use CreatedDateAndLastUpdatedTrait;

    public $table    = 'cached_value';

    //doesnt need reloading after save
    public $reload_after_save = false;


    protected function init(): void {

        parent::init();

        $this->addCreatedDateAndLastUpdateFields();
        $this->addCreatedDateAndLastUpdatedHook();

        $this->addFields(
            [
                [
                    'ident',
                    'type' => 'string',
                    'mandatory' => true
                ],
                [
                    'value',
                    'type' => 'string'
                ],
            ]
        );

        //if setting with ident exists, only update existing one
        //TODO: If somehow ON DUPLICATE KEY UPDATE is available in ATK, it would save a query
        $this->onHook(
            Model::HOOK_BEFORE_SAVE,
            function(Model $model, $isUpdate) {
                if($isUpdate) {
                    return;
                }
                $cv = $model->newInstance();
                $cv->tryLoadBy('ident', $model->get('ident'));
                if($cv->loaded()) {
                    $cv->set('value', $model->get('value'));
                    $cv->save();
                    $model->breakHook(false);
                }
            }
        );

        //special for CachedValue: Always set last_updated, even on insert.
        $this->onHook(
            Model::HOOK_BEFORE_SAVE,
            function (Model $model, $isUpdate) {
                $model->set('last_updated', new \DateTime());
            }
        );
    }
}
