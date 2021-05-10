<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuperController extends Controller
{
    public function getSupers(Request $request) {

        $pagination = $request->pagination ?? 10;
        $page = $request->page ?? 1;
        $order = $request->order ?? 'superheroes.name';
        $order_value = $request->order_value ?? 'desc';

        if($order == 'race') $order = 'races.name';
        if($order == 'publishers') $order = 'publishers.name';
        if($order == 'eyes') $order = 'eye_colors.name';
        if($order == 'hairs') $order = 'hair_colors.name';

        $firtsResult = ($page-1) * $pagination;

        $query = DB::table('superheroes')
            ->select('features.*','superheroes.*', 'races.name as race_name', 'publishers.name as publishers_name', 'eye_colors.name as eye_colors_name', 'hair_colors.name as hair_colors_name')
            ->join('features', 'features.superhero_id', '=', 'superheroes.id')
            ->leftJoin('races', 'features.race_id', '=', 'races.id')
            ->leftJoin('publishers', 'features.publisher_id', '=', 'publishers.id')
            ->leftJoin('eye_colors', 'features.eye_color_id', '=', 'eye_colors.id')
            ->leftJoin('hair_colors', 'features.hair_color_id', '=', 'hair_colors.id');

        // Para el filtrado podemos agregarle en forma de objeto dentro del array todas las opciones de los campos de la tabla, no teniendo limite en el mismo
        if(isset($request->filters)){
            foreach (json_decode($request->filters) as $key => $filter) {
                $query = $query->where($filter->name, $filter->value);
            }
        }

        if($order) $query = $query->orderBy($order, $order_value);
        
        // Para el paginado otra opcion tambien se pudiese utilizar chunk o el mismo paginate, pero veo esta la forma mas optima de realizarlo
        $supers = $query->skip($firtsResult)->take($pagination)->get()->toArray();

        return response()->json(['supers' => $supers], 200);
    }
}
