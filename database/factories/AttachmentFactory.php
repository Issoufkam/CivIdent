<?php

namespace Database\Factories;

use App\Models\Attachment;
use App\Models\Document;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttachmentFactory extends Factory
{
    protected $model = Attachment::class;

    public function definition(): array
    {
        return [
            'path' => 'attachments/' . $this->faker->uuid . '.pdf',
            'mime_type' => 'application/pdf',
            'document_id' => Document::factory(),
        ];
    }
}
