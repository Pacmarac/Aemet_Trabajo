<?php

namespace App\Http\Controllers;
use App\Models\Dato;
use App\Models\EstacionBd;
use App\Models\EstacionInv;
use Illuminate\Http\Request;
use Http;


class StatsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function recolectaInv(int $cantidad = 6)
    {
        $url = "https://opendata.aemet.es/opendata/api/valores/climatologicos/inventarioestaciones/todasestaciones";
        $response = Http::withHeaders([
            "API_KEY" => env("API_KEY")
        ])->get($url)["datos"];
        $datas = Http::get($response);
        $datas = mb_convert_encoding($datas, "UTF-8", "ISO-8859-1");
        $datajson = json_decode($datas, true);
        for ($i = 0; $i < $cantidad; $i++) {
            $estacion = new EstacionInv();
            if (EstacionInv::where('idema', $datajson[$i]['indicativo'])->doesntExist()) {
                $estacion->nombre = $datajson[$i]['nombre'];
                $estacion->idema = $datajson[$i]['indicativo'];
                $estacion->provincia = $datajson[$i]['provincia'];
                $estacion->latitud = dmsToDecimal($datajson[$i]['latitud']);
                $estacion->longitud = dmsToDecimal($datajson[$i]['longitud']);
                $estacion->altitud = $datajson[$i]['altitud'];
                $estacion->save();
            }
        }
        ;
        rellenar_Estacionbd();
    }

    /** podemos mirar si ejecutar cada hora o cada 12 horas ambas es posbile  */

    public function recolectaStat()
    {
        $estaciones = EstacionInv::all('id', 'idema');
        foreach ($estaciones as $estacion) {
            $url = "https://opendata.aemet.es/opendata/api/observacion/convencional/datos/estacion/{$estacion['idema']}";
            $response = Http::withHeaders([
                "API_KEY" => env("API_KEY")
            ])->get(url: $url)['datos'];
            $datasApi = Http::get($response);
            $datasApi = mb_convert_encoding($datasApi, "UTF-8", "ISO-8859-1");
            $datajson = json_decode($datasApi, true);
            $size = count($datajson);
            for ($i = 0; $i < $size; $i++) {
                if (
                    !Dato::where('idema', $estacion['idema'])
                        ->where('fecha', $datajson[$i]['fint'])
                        ->exists()
                ) {
                    $datosBd = new Dato();
                    $datosBd->id_estacion = $estacion['id'] ?? null;
                    $datosBd->idema = $estacion['idema'] ?? null;
                    $datosBd->fecha = $datajson[$i]['fint'] ?? null;
                    $datosBd->vv = $datajson[$i]['vv'] ?? null;
                    $datosBd->ta = $datajson[$i]['ta'] ?? null;
                    $datosBd->hr = $datajson[$i]['hr'] ?? null;
                    $datosBd->prec = $datajson[$i]['prec'] ?? null;
                    $datosBd->save();
                }

            }
        }
        return Dato::all();
    }

    /**
     * Procesa las stats
     */
    public function procesaStat()
    {
        //
    }
}

/**
 * Convierte una coordenada en formato DMS (DDMMSS + dirección)
 * a grados decimales.
 *
 * Ejemplo: "394744N" → 39 + 47/60 + 44/3600 ≈ 39.7956
 *
 * @param string $dms La cadena con el valor DMS.
 * @return float El valor en grados decimales.
 */
function dmsToDecimal(string $dms): float
{
    // Elimina espacios y convierte a mayúsculas
    $dms = strtoupper(trim($dms));
    // La última letra indica la dirección (N, S, E, W)
    $direction = substr($dms, -1);
    // Extrae la parte numérica (sin la letra)
    $dmsNumber = substr($dms, 0, -1);

    // Suponemos que el formato es siempre de 6 dígitos: DDMMSS
    // Si hay 6 dígitos, los dos primeros son grados, luego minutos y segundos.
    if (strlen($dmsNumber) == 6) {
        $degrees = (int) substr($dmsNumber, 0, 2);
        $minutes = (int) substr($dmsNumber, 2, 2);
        $seconds = (int) substr($dmsNumber, 4, 2);
    } else {
        // En caso de tener otro formato (por ejemplo, 7 dígitos para longitudes mayores),
        // podrías ajustar aquí cómo extraer los valores.
        // Por ejemplo: 3 dígitos para los grados y 2 para minutos y 2 para segundos.
        $degrees = (int) substr($dmsNumber, 0, 3);
        $minutes = (int) substr($dmsNumber, 3, 2);
        $seconds = (int) substr($dmsNumber, 5, 2);
    }

    // Conversión a decimal: grados + minutos/60 + segundos/3600
    $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

    // Si la dirección es Sur (S) o Oeste (W), el valor es negativo
    if ($direction === 'S' || $direction === 'W') {
        $decimal *= -1;
    }

    return round($decimal, 6);
}

function rellenar_Estacionbd()
{
    $estaciones = EstacionInv::all('id');
    foreach ($estaciones as $estacion) {
        $estacionBD = new EstacionBd();
        if (EstacionBd::where('id', $estacion['id'])->doesntExist()) {
            $estacionBD->id = $estacion['id'];
            $estacionBD->estado = 1;
            $estacionBD->save();
        }

    }
}