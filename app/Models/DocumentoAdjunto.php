<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DocumentoAdjunto extends Model
{
    protected $table = 'DOCUMENTOS_ADJUNTOS';

    protected $fillable = [
        'entrevista_id', 'nombre_archivo', 'ruta',
        'tipo_mime', 'tamanio_kb', 'subido_por',
    ];

    protected $casts = [
        'tamanio_kb' => 'integer',
    ];

    public function entrevista()
    {
        return $this->belongsTo(Entrevista::class, 'entrevista_id');
    }

    public function subidoPor()
    {
        return $this->belongsTo(Usuario::class, 'subido_por');
    }
}
