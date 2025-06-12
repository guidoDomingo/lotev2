<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotspot;

class HotspotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hotspots = [
            [
                'pitch' => 14.1,
                'yaw' => 1.5,
                'type' => 'info',
                'title' => 'Entrada Principal',
                'text' => 'Esta es la entrada principal del lote. Aquí encontrarás el acceso principal y la recepción.'
            ],
            [
                'pitch' => -10.5,
                'yaw' => 90.0,
                'type' => 'info',
                'title' => 'Zona Verde',
                'text' => 'Hermosa zona verde con árboles nativos y espacios para recreación.'
            ],
            [
                'pitch' => 5.0,
                'yaw' => -45.0,
                'type' => 'scene',
                'title' => 'Vista Panorámica',
                'text' => 'Desde aquí puedes apreciar una vista panorámica completa del terreno.'
            ],
            [
                'pitch' => 0.0,
                'yaw' => 180.0,
                'type' => 'info',
                'title' => 'Salida Posterior',
                'text' => 'Salida posterior que conecta con el área de servicios y estacionamiento.'
            ]
        ];

        foreach ($hotspots as $hotspot) {
            Hotspot::create($hotspot);
        }
    }
}
