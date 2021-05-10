<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\models\Superhero;
use App\models\EyeColor;
use App\models\HairColor;
use App\models\Publisher;
use App\models\Race;

class ImportFile extends Command
{   
    public $data = [];
    public $features = [];
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:csv-super';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importamos el csv para organizar a los super';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {   
        print_r("Iniciamos importacion - ". date("h:i:sa")); echo "\n"; 
        
        // Primera iteracion del documento
        $this->readDoc('processMasterRecords');

        // Almaceno los datos de lo que serian las tablas principales
        foreach ($this->data as $key => $row) $this->FlipDataAndSave($this->data[$key], $key);

        // Obtengo los datos maestros almacenados
        $this->getMasterRecords();

        // Almaceno el resto de las caracteristicas de los super, segunda iteracion del documento
        $this->readDoc('featureRecords');

        // Terminamos de almacenar el restante de los datos en la tabla Features
        $this->saveData('features', $this->features);

        print_r("Finalizamos importacion - ". date("h:i:sa")); echo "\n"; 
    }

    private function readDoc($methodCalled){

        $dir = '/var/www/laravel-app/csv/superheros.csv';
        print_r("ruta definida: {$dir}"); echo "\n"; 
    
        if(is_file($dir)) {
            $handle = fopen($dir, "r");
            $row = 0;
            while ($record = fgetcsv($handle, 1000, ",")) {
                if($row){
                    $this->{$methodCalled}($record);
                }
                $row++;
            }
            
            fclose($handle);
        }
        else{
            print_r("Ubicacion del archivo no encontrada");
        }
    }

    // Procedemos a llenar los distintos arreglos con los datos que considero maestros
    // Otra forma de hacerlo, seria ir llenado un arreglo simple y luego validar con if in_array si no existe para proceder a agregar el dato
    private function processMasterRecords($record){
        $this->data['superheroes'][$record[1]] = '';
        if($record[8] != '')   $this->data['races'][$record[8]] = '';
        if($record[13] != '-') $this->data['eye_colors'][$record[13]] = '';
        if($record[14] != '-') $this->data['hair_colors'][$record[14]] = '';
        if($record[15] != '')  $this->data['publishers'][$record[15]] = '';
    }

    private function FlipDataAndSave($records, $model){
        $data = [];
        foreach ($records as $key => $row) {
           $data[]['name'] = $key;  
        }
        $this->saveData($model, $data);
    }

    private function saveData($model, $data){
        DB::table($model)->insert($data);
    }

    private function getMasterRecords(){
        $this->data = [];
        $this->data['superheroes'] = Superhero::pluck('id', 'name')->toArray();
        $this->data['races'] = Race::pluck('id', 'name')->toArray();
        $this->data['eye_colors'] = EyeColor::pluck('id', 'name')->toArray();
        $this->data['hair_colors'] = HairColor::pluck('id', 'name')->toArray();
        $this->data['publishers'] = Publisher::pluck('id', 'name')->toArray();
    }

    /* En el caso de la importacion de height otra forma de hacerlo es solo importar 1 columna de cada 1 y convertir el dato a cm, ft o lo que se desee,
       de esta forma nos ahorramos tener 1 columna adicional en la tabla. */
    /* De igual forma si la estructura de datos fuese mucho mas compleja y con muchos mas datos, 
       en ese caso es preferible usar otra comparacion de datos en la condicion, 
       es decir ir comparando si el dato cumple el formato "numero cm" por ejemplo y de no hacerlo que lo imprima por pantalla,
       de esta manera podemos ir verificando mientras se van importando los datos que no pasemos por alto algun formato extrano o que no hayamos visualizado */
    /* El mismo caso de uso se puede hacer con el atributo weight */
    private function featureRecords($record){
        static $c = 0;

        $this->features[$c]['superhero_id'] = $this->data['superheroes'][$record[1]];
        ($record[2] != '') ? $this->features[$c]['full_name'] = $record[2] : $this->features[$c]['full_name'] = NULL;
        $this->features[$c]['strength'] = $record[3];
        $this->features[$c]['speed'] = $record[4];
        $this->features[$c]['durability'] = $record[5];
        $this->features[$c]['power'] = $record[6];
        $this->features[$c]['combat'] = $record[7];
        ($record[9] != '-') ? $this->features[$c]['height_ft'] = $record[9] : $this->features[$c]['height_ft'] = '';
        ($record[10] != '') ? $this->features[$c]['height_cm'] = $record[10] : $this->features[$c]['height_cm'] = '' ;
        ($record[11] != '- lb') ? $this->features[$c]['weight_lb'] = $record[11] : $this->features[$c]['weight_lb'] = '';
        ($record[12] != '') ? $this->features[$c]['weight_kg'] = $record[12] : $this->features[$c]['weight_kg'] = '';
        ($record[8] != '') ? $this->features[$c]['race_id'] = $this->data['races'][$record[8]] : $this->features[$c]['race_id'] = NULL;
        ($record[13] != '-') ? $this->features[$c]['eye_color_id'] = $this->data['eye_colors'][$record[13]] : $this->features[$c]['eye_color_id'] = NULL;
        ($record[14] != '-') ? $this->features[$c]['hair_color_id'] = $this->data['hair_colors'][$record[14]] : $this->features[$c]['hair_color_id'] = NULL;
        ($record[15] != '') ? $this->features[$c]['publisher_id'] = $this->data['publishers'][$record[15]] : $this->features[$c]['publisher_id'] = NULL;

        // En un documento con muchos registros de esta manera no matamos la memoria y procesamos mucho mas rapido, que en vez de insertar 1 a la vez
        if(( $c % 50 ) == 0){
            $this->saveData('features', $this->features);
            $this->features = [];
        }

        $c++;
    }


}
