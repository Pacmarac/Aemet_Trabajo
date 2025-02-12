<?php

namespace Database\Seeders;

use App\Models\EstacionInv;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatoSeeder extends Seeder {
  /**
   * Run the database seeds.
   */
  public function run(): void {
    $datos = [
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-06T21:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 0.0,
        "vv" => 0.0,
        "dv" => 0.0,
        "lat" => 39.823338,
        "dmax" => 112.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => 1.0,
        "ta" => 1.1,
        "tamax" => 1.7,
        "rviento" => 0.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-06T22:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 0.0,
        "vv" => 0.0,
        "dv" => 0.0,
        "lat" => 39.823338,
        "dmax" => 91.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => 0.5,
        "ta" => 0.5,
        "tamax" => 1.2,
        "rviento" => 0.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-06T23:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 0.0,
        "vv" => 0.0,
        "dv" => 0.0,
        "lat" => 39.823338,
        "dmax" => 81.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => 0.5,
        "ta" => 1.2,
        "tamax" => 1.2,
        "rviento" => 0.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-07T00:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 0.0,
        "vv" => 0.0,
        "dv" => 0.0,
        "lat" => 39.823338,
        "dmax" => 115.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => 1.2,
        "ta" => 1.2,
        "tamax" => 1.4,
        "rviento" => 0.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-07T01:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 0.0,
        "vv" => 0.0,
        "dv" => 0.0,
        "lat" => 39.823338,
        "dmax" => 129.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => 0.6,
        "ta" => 0.6,
        "tamax" => 1.3,
        "rviento" => 0.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-07T02:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 0.0,
        "vv" => 0.0,
        "dv" => 0.0,
        "lat" => 39.823338,
        "dmax" => 215.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => 0.0,
        "ta" => 0.0,
        "tamax" => 0.6,
        "rviento" => 0.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-07T03:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 0.0,
        "vv" => 0.0,
        "dv" => 0.0,
        "lat" => 39.823338,
        "dmax" => 20.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => -0.3,
        "ta" => -0.3,
        "tamax" => 0.1,
        "rviento" => 0.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-07T04:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 1.2,
        "vv" => 0.0,
        "dv" => 0.0,
        "lat" => 39.823338,
        "dmax" => 235.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => -0.3,
        "ta" => -0.3,
        "tamax" => 0.0,
        "rviento" => 2.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-07T05:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 0.0,
        "vv" => 0.0,
        "dv" => 0.0,
        "lat" => 39.823338,
        "dmax" => 320.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => -0.5,
        "ta" => -0.5,
        "tamax" => -0.3,
        "rviento" => 0.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-07T06:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 0.0,
        "vv" => 0.0,
        "dv" => 0.0,
        "lat" => 39.823338,
        "dmax" => 128.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => -0.6,
        "ta" => -0.1,
        "tamax" => 0.0,
        "rviento" => 0.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-07T07:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 2.4,
        "vv" => 0.7,
        "dv" => 117.0,
        "lat" => 39.823338,
        "dmax" => 178.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => -0.3,
        "ta" => 0.5,
        "tamax" => 0.5,
        "rviento" => 9.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-07T08:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 4.0,
        "vv" => 0.3,
        "dv" => 95.0,
        "lat" => 39.823338,
        "dmax" => 154.0,
        "ubi" => "LLUC",
        "hr" => 99.0,
        "tamin" => 0.3,
        "ta" => 3.6,
        "tamax" => 3.6,
        "rviento" => 17.0
      ],
      [
        "idema" => "B013X",
        "lon" => 2.885828,
        "fint" => "2025-02-07T09:00:00+0000",
        "prec" => 0.0,
        "alt" => 490.0,
        "vmax" => 4.0,
        "vv" => 0.1,
        "dv" => 263.0,
        "lat" => 39.823338,
        "dmax" => 162.0,
        "ubi" => "LLUC",
        "hr" => 72.0,
        "tamin" => 3.7,
        "ta" => 9.4,
        "tamax" => 9.4,
        "rviento" => 27.0
      ]
    ];

    $datos = array_map(function ($dato) {
      $estacion = EstacionInv::where('idema', $dato["idema"])->first();

      $newArr = [
        "id_estacion" => $estacion["id"],
        "idema" => $dato["idema"],
        "fecha" => Carbon::parse($dato["fint"])->format('Y-m-d H:i:s')
      ];

      if (isset($dato["vv"])) $newArr["vv"] = $dato["vv"];
      if (isset($dato["ta"])) $newArr["ta"] = $dato["ta"];
      if (isset($dato["hr"])) $newArr["hr"] = $dato["hr"];
      if (isset($dato["prec"])) $newArr["prec"] = $dato["prec"];

      return $newArr;
    }, $datos);


    DB::table('datos')->insert($datos);
  }
}
