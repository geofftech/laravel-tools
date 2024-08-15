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
                    $this->deleteFile($fileName);
                }
            } else {
                // field name + descriptor = search JSON for values of this prop
                $this->onUpdateJsonField($index, $fieldName);
            }
        }
    }

    private function onUpdateJsonField($fieldName, $properties)
    {
        // field name + string = split by "," into an array
        if (is_string($properties)) {
            $properties = explode(",", $properties);
        }

        // filed name + function = returns array of values
        if (is_callable($properties)) {
            $properties = $properties();
        }

        // field name + array = scan json for these fields
        $newFileNames = $this->extractFileNames($properties, $this->getOriginal($fieldName));
        $oldFileNames = $this->extractFileNames($properties, $this->{$fieldName});

        $missingFileNames = array_diff($newFileNames, $oldFileNames);

        // delete all files
        foreach ($missingFileNames as $fileName) {
            $this->deleteFile($fileName);
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

                $this->deleteFile($fileName);
            } else {
                // field name + descriptor = search JSON for values of this prop
                $this->onDeleteJsonField($index, $fieldName);
            }
        }
    }

    private function onDeleteJsonField($fieldName, $properties)
    {
        // field name + string = split by "," into an array
        if (is_string($properties)) {
            $properties = explode(",", $properties);
        }

        // filed name + function = returns array of values
        if (is_callable($properties)) {
            $properties = $properties();
        }

        // field name + array = scan json for these fields
        $json = $this->getOriginal($fieldName);
        $fileNames = $this->extractFileNames($properties, $json);

        // delete all files
        foreach ($fileNames as $fileName) {
            $this->deleteFile($fileName);
        }
    }

    private function iterate(&$fileNames, $properties, $json)
    {
        if (!$json) return;

        foreach ($json as $key => $value) {
            if (is_array($value)) {
                $this->iterate($fileNames, $properties, $value);
            } else if (is_string($value) && in_array($key, $properties)) {
                $fileNames[] = $value;
            }
        }
    }

    public function extractFileNames($properties, $json)
    {
        $fileNames = [];

        $this->iterate($fileNames, $properties, $json);

        return array_unique($fileNames);
    }

    public function deleteFile($fileName)
    {
        if (!is_null($fileName)) {
            Log::info('deleting ' . $fileName);
            Storage::disk('public')->delete($fileName);
        }
    }
}
