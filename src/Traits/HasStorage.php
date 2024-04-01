<?php

namespace GeoffTech\LaravelTools\Traits;

use Illuminate\Support\Facades\Storage;
use Log;

trait HasStorage
{

  // protected $storage = [];

  public function clearStorageOnUpdate(): void
  {
    // error_log('clearStorageOnUpdate ' . $this->id);

    if (is_null($this->storage)) {
      return;
    }

    foreach ($this->storage as $fieldName) {
      $fileName = $this->getOriginal($fieldName);
      if ($this->isDirty($fieldName) && !is_null($fileName)) {
        Log::info('deleting ' . $fileName);
        Storage::disk('public')->delete($fileName);
      }
    }
  }

  public function clearStorageOnDelete(): void
  {
    // error_log('clearStorageOnDelete ' . $this->id);

    if (is_null($this->storage)) {
      return;
    }

    foreach ($this->storage as $fieldName) {
      $fileName = $this->{$fieldName};
      if (!is_null($this->{$fieldName})) {
        Log::info('deleting ' . $fileName);
        Storage::disk('public')->delete($fileName);
      }
    }

  }

  public static function bootHasStorage()
  {

    $isUsingSoftDeletes = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses(self::class));

    self::updated(function ($model) {
      $model->clearStorageOnUpdate();
    });

    if (!$isUsingSoftDeletes) {
      self::deleted(function ($model) {
        $model->clearStorageOnDelete();
      });
    } else {
      self::forceDeleted(function ($model) {
        $model->clearStorageOnDelete();
      });
    }
  }

}
