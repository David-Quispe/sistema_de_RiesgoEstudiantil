{{--
    Partial reutilizable: envoltorio visual de correos SMER.
    Uso: @include('emails.layout-wrapper', ['titulo' => '...'])  -- NO se usa solo, ver vistas reporte-ejecutivo.blade.php etc.
    Este archivo queda como referencia de estilos; las vistas finales incluyen su propio <table> wrapper inline
    para máxima compatibilidad con clientes de correo (Gmail/Outlook no soportan bien componentes Blade anidados en mail).
--}}
