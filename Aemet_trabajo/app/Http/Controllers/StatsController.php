<?php

namespace App\Http\Controllers;

use App\Models\Dato;
use App\Models\Stat;
use App\Models\EstacionBd;
use App\Models\EstacionInv;
use Illuminate\Support\Facades\DB;
use Http;

/**
 * Class StatsController
 *
 * Controlador encargado de recolectar, procesar y almacenar estadísticas climáticas.
 */
class StatsController extends Controller
{
    /**
     * Recolecta el inventario de estaciones de la AEMET y las guarda en la base de datos.
     *
     * Se conecta a la API de AEMET para obtener el inventario de estaciones y,
     * para cada estación (limitado por $cantidad), verifica si no existe ya en la base
     * de datos (según el campo 'indicativo'). Si no existe, convierte las coordenadas
     * del formato DMS a decimal y almacena la información en la tabla EstacionInv.
     * Finalmente, se invoca la función rellenar_Estacionbd() para completar la tabla
     * destino con un estado inicial.
     *
     * @param int $cantidad La cantidad de estaciones a procesar (por defecto 5).
     * @return void
     */
    public function recolectaInv(int $cantidad = 5)
    {
        // URL de la API que retorna el inventario de estaciones
        $url = "https://opendata.aemet.es/opendata/api/valores/climatologicos/inventarioestaciones/todasestaciones";

        // Se realiza la petición a la API usando la API_KEY definida en el entorno
        $response = Http::withHeaders([
            "API_KEY" => env("API_KEY")
        ])->get($url)["datos"];

        // Se obtiene el contenido de la URL proporcionada en la respuesta
        $datas = Http::get($response);//como se llama la funcion de aissa y david
        // Convertir la codificación a UTF-8 (desde ISO-8859-1)
        $datas = mb_convert_encoding($datas, "UTF-8", "ISO-8859-1");
        // Se decodifica el JSON a un array asociativo
        $datajson = json_decode($datas, true);

        // Se procesan las estaciones según la cantidad especificada
        for ($i = 0; $i < $cantidad; $i++) {
            $estacion = new EstacionInv();
            // Si la estación (según su 'indicativo') no existe, se inserta
            if (EstacionInv::where('idema', $datajson[$i]['indicativo'])->doesntExist()) {
                $estacion->nombre = $datajson[$i]['nombre'];
                $estacion->idema = $datajson[$i]['indicativo'];
                $estacion->provincia = $datajson[$i]['provincia'];
                // Convierte las coordenadas en formato DMS a grados decimales
                $estacion->latitud = dmsToDecimal($datajson[$i]['latitud']);
                $estacion->longitud = dmsToDecimal($datajson[$i]['longitud']);
                $estacion->altitud = $datajson[$i]['altitud'];
                $estacion->save();
            }
        }
    }

    /**
     * Recolecta los datos de observación de cada estación y los almacena en la tabla Dato.
     *
     * Por cada estación registrada en EstacionInv, se realiza una solicitud a la API de AEMET
     * para obtener los datos de observación. Si no existe un registro previo en Dato para esa
     * estación y fecha (fint), se almacena el nuevo registro.
     *
     * @return \Illuminate\Database\Eloquent\Collection Colección con todos los registros de Dato.
     */
    public function recolectaStat()
    {
        // Se obtienen todas las estaciones registradas (id e idema)
        $innerjoin = EstacionInv::join('estacion_bd','estacion_bd.id','=','estacion_inv.id')
        ->select('estacion_inv.id','estacion_inv.idema')
        ->where('estacion_bd.estado','=',1)
        ->getQuery()
        ->get();
        $estaciones = json_decode($innerjoin,true);

        foreach ($estaciones as $estacion) {
            // Construir la URL para obtener datos de observación de la estación
            $url = "https://opendata.aemet.es/opendata/api/observacion/convencional/datos/estacion/{$estacion['idema']}";
            $response = Http::withHeaders([
                "API_KEY" => env("API_KEY")
            ])->get(url: $url)['datos'];

            $datasApi = Http::get($response);
            $datasApi = mb_convert_encoding($datasApi, "UTF-8", "ISO-8859-1");
            $datajson = json_decode($datasApi, true);
            $size = count($datajson);

            // Se recorren los registros de datos obtenidos de la API
            for ($i = 0; $i < $size; $i++) {
                // Si no existe un registro en Dato para la misma estación y fecha, se guarda
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
        // Retorna todos los registros de la tabla Dato
        return Dato::all();
    }

    /**
     * Procesa y agrupa las estadísticas por hora y semana.
     *
     * Para cada hora del día (00:00 a 23:00), se agrupan los registros en la tabla Dato
     * según la estación (idema), la semana (WEEK(fecha)) y el año (YEAR(fecha)). Se calculan
     * los promedios de los valores (vv, ta, hr, prec) y se determina el primer día de la semana.
     *
     * @return \Illuminate\Http\JsonResponse JSON con los resultados agregados agrupados por hora.
     */
    public function procesaStat()
    {
        $results = [];
        // Se recorre cada hora del día
        for ($i = 0; $i < 24; $i++) {
            // Se formatea la hora para que tenga el formato "HH:00"
            $hour = sprintf("%02d:00", $i);

            $results[$hour] = Dato::select(
                DB::raw('MIN(id_estacion) as id_estacion'),
                'idema',
                DB::raw('AVG(vv) as media_vv'),
                DB::raw('AVG(ta) as media_ta'),
                DB::raw('AVG(hr) as media_hr'),
                DB::raw('AVG(prec) as media_prec'),
                // Se obtiene el primer día de la semana restando el número de días correspondiente (suponiendo lunes como inicio)
                DB::raw('MIN(DATE_SUB(fecha, INTERVAL WEEKDAY(fecha) DAY)) as start_of_week')
            )
                ->where(DB::raw("DATE_FORMAT(fecha, '%H:%i')"), '=', $hour)
                ->groupBy('idema', DB::raw('WEEK(fecha)'), DB::raw('YEAR(fecha)'))
                ->orderBy('idema')
                ->get();
        }

        // Retorna los resultados agrupados en formato JSON
        return response()->json($results);
    }

    /**
     * Almacena las estadísticas procesadas en la tabla Stat.
     *
     * Obtiene los datos agregados a través de procesaStat(), recorre cada resultado y
     * verifica que la combinación de idema y fecha (primer día de la semana) no exista ya en Stat.
     * Si la combinación no existe, inserta el registro en la tabla Stat.
     *
     * @return \Illuminate\Database\Eloquent\Collection Colección con todos los registros de Stat.
     */
    public function almacenaStat()
    {
        // Se obtiene el JSON de estadísticas procesadas
        $response = $this->procesaStat();
        // Se decodifica el JSON a un array asociativo
        $data = json_decode($response->getContent(), true);

        // Se recorre cada conjunto de datos agrupados por hora
        foreach ($data as $horas) {
            // Cada $horas es un array de resultados para esa hora
            foreach ($horas as $datos_horas) {
                // Si no existe en Stat la combinación de idema y start_of_week (fecha), se inserta
                if (
                    !Stat::where('idema', $datos_horas['idema'])
                        ->where('fecha', $datos_horas['start_of_week'])
                        ->exists()
                ) {
                    $stat = new Stat();
                    $stat->id_estacion = $datos_horas['id_estacion'];
                    $stat->idema = $datos_horas['idema'];
                    $stat->fecha = $datos_horas['start_of_week'];
                    $stat->vv = $datos_horas['media_vv'];
                    $stat->ta = $datos_horas['media_ta'];
                    $stat->hr = $datos_horas['media_hr'];
                    $stat->prec = $datos_horas['media_prec'];
                    $stat->save();
                }
            }
        }
        // Retorna todos los registros almacenados en la tabla Stat
        return Stat::all();
    }
}

/**
 * Convierte una coordenada en formato DMS (grados, minutos y segundos con dirección)
 * a grados decimales.
 *
 * Ejemplo: "394744N" se convierte a 39.7956.
 *
 * @param string $dms La cadena en formato DMS (por ejemplo, "394744N" o "394744N").
 * @return float El valor de la coordenada en grados decimales.
 */
function dmsToDecimal(string $dms): float
{
    // Elimina espacios y convierte la cadena a mayúsculas
    $dms = strtoupper(trim($dms));
    // La última letra indica la dirección (N, S, E, W)
    $direction = substr($dms, -1);
    // Extrae la parte numérica (sin la letra de dirección)
    $dmsNumber = substr($dms, 0, -1);

    // Se asume que el formato es de 6 dígitos: DDMMSS
    if (strlen($dmsNumber) == 6) {
        $degrees = (int) substr($dmsNumber, 0, 2);
        $minutes = (int) substr($dmsNumber, 2, 2);
        $seconds = (int) substr($dmsNumber, 4, 2);
    } else {
        // Para formatos diferentes (por ejemplo, 7 dígitos para longitudes mayores),
        // se asume que los 3 primeros dígitos corresponden a grados, 2 a minutos y 2 a segundos.
        $degrees = (int) substr($dmsNumber, 0, 3);
        $minutes = (int) substr($dmsNumber, 3, 2);
        $seconds = (int) substr($dmsNumber, 5, 2);
    }

    // Conversión a grados decimales: grados + (minutos/60) + (segundos/3600)
    $decimal = $degrees + ($minutes / 60) + ($seconds / 3600);

    // Si la dirección es Sur (S) u Oeste (W), se convierte el valor a negativo
    if ($direction === 'S' || $direction === 'W') {
        $decimal *= -1;
    }

    return round($decimal, 6);
}

/**
 * Rellena la tabla de estaciones destino (EstacionBd) utilizando los registros
 * existentes en EstacionInv.
 *
 * Para cada estación en EstacionInv, verifica si ya existe en EstacionBd y, si no,
 * la inserta asignándole un estado inicial (por ejemplo, 1).
 *
 * @return void
 */