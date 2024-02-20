<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Http\UploadedFile;

class File extends Model
{
    use HasFactory;

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

    public static function upload(UploadedFile $file, string $name, Model $model): static
    {
        $model->files()->where('name', '=', $name)->delete();

        $self = new self;
        $self->external_id = uuid_create();
        $self->name = $name;
        $self->model()->associate($model);
        $self->type = $file->getClientMimeType();
        $self->size = $file->getSize();
        $self->path = $file->storeAs("kyc", uuid_create() . "." . $file->getClientOriginalExtension(), ['disk' => 'public']);
        $self->save();

        return $self->refresh();
    }
}
