<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\CategoriaProducto;

class CategoriaProductoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
       $categorias = [
            ['nombre' => 'General',     'color' => '#6B7280'],
            ['nombre' => 'Electrónica', 'color' => '#3B82F6'],
            ['nombre' => 'Alimentos',   'color' => '#10B981'],
            ['nombre' => 'Ropa',        'color' => '#8B5CF6'],
            ['nombre' => 'Ferretería',  'color' => '#F59E0B'],
            ['nombre' => 'Servicios',   'color' => '#EC4899'],
        ];

        foreach ($categorias as $cat) {
            CategoriaProducto::firstOrCreate(['nombre' => $cat['nombre']], $cat);
        }

        $this->command->info('✅ Categorías de productos creadas.');    
    }
}
