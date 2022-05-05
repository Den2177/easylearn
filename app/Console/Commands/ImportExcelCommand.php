<?php

namespace App\Console\Commands;

use App\Imports\WordsImport;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;

class ImportExcelCommand extends Command
{
    protected $signature = 'import:excel';
    protected $description = 'Get data from excel';

    public function handle()
    {
        ini_set('memory_limit', '-1');
        Excel::import(new WordsImport(), public_path('excel/words.xlsx'));
        dump("finished");
    }
}
