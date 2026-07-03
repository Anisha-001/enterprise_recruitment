<?php

namespace App\Filament\Resources\ApplicationResource\Pages;

use App\Filament\Resources\ApplicationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditApplication extends EditRecord
{
    protected static string $resource = ApplicationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $oldStatus = $record->status;
        $newStatus = $data['status'] ?? null;

        if ($newStatus && $newStatus !== $oldStatus) {
            // Update non-status columns first
            $statusFields = ['status', 'rejection_reason', 'rejection_notes'];
            $otherData = array_diff_key($data, array_flip($statusFields));
            $record->update($otherData);

            // Execute service status transition
            $notes = $newStatus === 'rejected' ? ($data['rejection_notes'] ?? null) : null;
            $record = app(\App\Services\Application\ApplicationService::class)
                ->transitionStatus($record, $newStatus, $notes, auth()->id());

            if ($newStatus === 'rejected') {
                $record->update(['rejection_reason' => $data['rejection_reason'] ?? null]);
            }
        } else {
            $record->update($data);
        }

        return $record;
    }
}
