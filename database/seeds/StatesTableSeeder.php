<?php
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Bican\Roles\Models\Role;
use App\User as User;
use Flynsarmy\CsvSeeder\CsvSeeder;

class StatesTableSeeder extends Seeder {

    public function run()
    {
        // Uncomment the below to wipe the table clean before populating
        // DB::table('areas')->delete();

        function csv_to_array($filename='', $delimiter=',')
        {
            if(!file_exists($filename) || !is_readable($filename))
                return FALSE;

            $header = NULL;
            $data = array();
            if (($handle = fopen($filename, 'r')) !== FALSE)
            {
                while (($row = fgetcsv($handle, 1000, $delimiter)) !== FALSE)
                {
                    if(!$header)
                        $header = $row;
                    else
                        $data[] = array_combine($header, $row);
                }
                fclose($handle);
            }
            return $data;
        }

        /****************************************
         * CSV FILE SAMPLE *
         ****************************************/
        // id,subdireccion_id,idInterno,area,deleted_at,created_at,updated_at
        // ,1,4,AREA MALAGA OCC.,,2013/10/13 10:27:52,2013/10/13 10:27:52
        // ,1,2,AREA MALAGA N/ORIENT,,2013/10/13 10:27:52,2013/10/13 10:27:52

        $csvFile = public_path().'/states.csv';

        $areas = csv_to_array($csvFile);


        // Uncomment the below to run the seeder
        DB::table('states')->insert($areas);
    }

}