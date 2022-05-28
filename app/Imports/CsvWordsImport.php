<?php

namespace App\Imports;

use App\Models\Word;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;

class CsvWordsImport implements ToModel, WithCustomCsvSettings
{
    private $dictionaryId;

    public function __construct($id)
    {
        $this->dictionaryId = $id;
    }

    public function model(array $row)
    {
        if ($row['0'] === 'eng' && $row['1'] === 'rus') {
            return null;
        }

        $image = null;

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/storage/app/public/images/' . $row['0'] . ('.png'))) {
            $image = '/storage/app/public/images/' . $row['0'] . '.png';
        }

        if (file_exists($_SERVER['DOCUMENT_ROOT'] . '/storage/app/public/images/' . $row['0'] . ('.jpg'))) {
            $image = '/storage/app/public/images/' . $row['0'] . '.jpg';
        }

        return new Word([
            'eng' => $row['0'],
            'rus' => $row['1'],
            'image' => $image,
            'dictionary_id' => $this->dictionaryId,
        ]);
    }

    public function getCsvSettings(): array
    {
        return [
            'input_encoding' => 'Windows-1251',
            'delimiter' => ";",
        ];
    }
}
