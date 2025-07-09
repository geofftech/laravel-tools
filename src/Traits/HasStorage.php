<?php

namespace GeoffTech\LaravelTools\Traits;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

trait HasStorage
{
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

    public function clearStorageOnUpdate(): void
    {
        if (is_null($this->storage)) {
            return;
        }

        foreach ($this->storage as $index => $fieldName) {
            if (is_numeric($index)) {
                // numeric id + field name = use this value exactly
                $fileName = $this->getOriginal($fieldName);

                if ($this->isDirty($fieldName)) {
                    $this->hasStorageDeleteFile($fieldName, $fileName);
                }
            } else {
                // field name + descriptor = search JSON for values of this prop
                $this->clearStorageOnUpdateJsonField($index, $fieldName);
            }
        }
    }

    private function clearStorageOnUpdateJsonField($fieldName, $properties)
    {
        // field name + string = split by "," into an array
        if (is_string($properties)) {
            $properties = explode(',', $properties);
        }

        // filed name + function = returns array of values
        if (is_callable($properties)) {
            $properties = $properties();
        }

        // field name + array = scan json for these fields
        $newFileNames = $this->hasStorageExtractFileNames($properties, $this->getOriginal($fieldName));
        $oldFileNames = $this->hasStorageExtractFileNames($properties, $this->{$fieldName});

        $missingFileNames = array_diff($newFileNames, $oldFileNames);

        // delete all files
        foreach ($missingFileNames as $fileName) {
            $this->hasStorageDeleteFile($fieldName, $fileName);
        }
    }

    public function clearStorageOnDelete(): void
    {
        if (is_null($this->storage)) {
            return;
        }

        foreach ($this->storage as $index => $fieldName) {
            if (is_numeric($index)) {
                // numeric id + field name = use this value exactly
                $fileName = $this->getOriginal($fieldName);

                $this->hasStorageDeleteFile($fieldName, $fileName);
            } else {
                // field name + descriptor = search JSON for values of this prop
                $this->clearStorageOnDeleteJsonField($index, $fieldName);
            }
        }
    }

    private function clearStorageOnDeleteJsonField($fieldName, $properties)
    {
        // field name + string = split by "," into an array
        if (is_string($properties)) {
            $properties = explode(',', $properties);
        }

        // filed name + function = returns array of values
        if (is_callable($properties)) {
            $properties = $properties();
        }

        // field name + array = scan json for these fields
        $json = $this->getOriginal($fieldName);
        $fileNames = $this->hasStorageExtractFileNames($properties, $json);

        // delete all files
        foreach ($fileNames as $fileName) {
            $this->hasStorageDeleteFile($fieldName, $fileName);
        }
    }

    private function hasStorageIterate(&$fileNames, $properties, $json)
    {
        if (!$json) {
            return;
        }

        foreach ($json as $key => $value) {
            if (is_array($value)) {
                $this->hasStorageIterate($fileNames, $properties, $value);
            } elseif (is_string($value) && in_array($key, $properties)) {
                $fileNames[] = $value;
            }
        }
    }

    public function hasStorageExtractFileNames($properties, $json)
    {
        $fileNames = [];

        $this->hasStorageIterate($fileNames, $properties, $json);

        return array_unique($fileNames);
    }

    public function hasStorageDeleteFile($fieldName, $fileName)
    {
        if (!is_null($fileName)) {
            $disk = $this->hasStorageGetDisk($fieldName);

            Log::info('storage:delete', [
                'file' => $fileName,
                'disk' => $disk,
            ]);

            Storage::disk($disk)->delete($fileName);
        }
    }

    public function hasStorageGetDisk($fieldName)
    {
        $default = config('filesystems.default');

        $disk = $this->storage_disk ?? $default;

        if (is_array($disk)) {
            return $disk[$fieldName] ?? $default;
        }

        return $disk;
    }
}
