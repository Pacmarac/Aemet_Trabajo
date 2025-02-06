<?php

namespace App\Http\Controllers;
use App\Models\Dato;
use Illuminate\Http\Request;
use Http;


class StatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function recolectaInv()
    {
        $url = "https://opendata.aemet.es/opendata/api/valores/climatologicos/inventarioestaciones/todasestaciones";
        $response = Http::withHeaders([
            "API_KEY" => env("API_KEY")
        ])->get($url)["datos"];
        $datas = Http::get($response);
        $datas = mb_convert_encoding($datas,"UTF-8", "auto");
        $datajson = json_decode($datas, true);
         foreach( $datajson as $valor){
            $info[] = [
                'nombre' => $valor['nombre'] ?? null,
                'idema'    => $valor['indicativo'] ?? null,
                'provincia'    => $valor['provincia'] ?? null,  // Asigna null si no existe 'ta'
                'latitud'    => $valor['latitud'] ?? null,
                'longitud'  => $valor['longitud'] ?? null,
                'altitud'  => $valor['altitud'] ?? null
            ];
        }; 
        return $info;
       
    }

    /**
     * Show the form for creating a new resource.
     */
    public function almacenaStat()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function procesaStat()
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        
    }
}
