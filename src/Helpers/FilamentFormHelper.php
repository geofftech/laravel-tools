<?php

namespace App\helpers;

use Filament\Forms\Components\Actions\Action;

class FilamentFormHelper
{

  /** 
   * Select Link
   * 
   * - added a link to the records edit form after the select component
   * 
   * usage:
   *   ->suffix(fn() => FilamentFormHelper::LinkReferencedItem($form, 'plan_id', EditPlan::class)),
   * 
   */

  public static function LinkReferencedItem($form, string $fieldName, $editForm)
  {
    $value = $form->getRecord()?->{$fieldName};
    return (!$value)
      ? null
      : Action::make($fieldName . '_link')
        ->iconButton()
        ->icon('heroicon-m-arrow-top-right-on-square')
        ->url(function () use ($value, $editForm): string {
          return $editForm::getUrl([$value]);
        });
  }

}
