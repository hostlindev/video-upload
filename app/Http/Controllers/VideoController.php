<?php

namespace App\Http\Controllers;

use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:30',
            'lastname' => 'required|string|max:30',
            'dni' => 'required|string|max:15',
            'video' => 'required|file|mimetypes:video/mp4,video/x-msvideo,video/quicktime|max:102400', // x-msvideo para AVI, // tamaño máximo de archivo en KB (100 MB aquí)
        ]);

        $userIp = $request->ip();

        // Iniciar una transacción de base de datos
        DB::beginTransaction();

        try {
            // Procesar el archivo de video
            $convertedUrl = $this->convertVideo($request->file('video'));

            if (!$convertedUrl) {
                throw new \Exception('No se pudo cargar el video o convertirlo.');
            }

            // Crear el registro del video en la base de datos dentro de la transacción
            $video = Video::create([
                'nombre' => $data['name'],
                'apellido' => $data['lastname'],
                'cedula' => $data['dni'],
                'enlace' => $convertedUrl, // Guardar la ruta del archivo en la columna 'enlace'
                'ip' => $userIp, // Guardar la IP del usuario en la columna 'ip'
            ]);

            if (!$video) {
                throw new \Exception('Error al guardar el video');
            }

            // Confirmar la transacción si todo ha ido bien
            DB::commit();

            return response()->json(['message' => 'Datos guardados exitosamente', 'video_url' => $convertedUrl], 201);
        } catch (\Exception $e) {
            // Deshacer la transacción en caso de error
            DB::rollBack();

            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    private function convertVideo($file)
    {
        if (!$file) {
            Log::error('Ningun archivo recibido');
            return null;
        }

        try {
            $originalExtension = $file->getClientOriginalExtension();
            Log::info('Original file extension: ' . $originalExtension);

            // Verificar si el archivo ya está en formato MP4
            if ($originalExtension === 'mp4') {
                $originalPath = Storage::disk('public')->path($file->store('videos/original', 'public'));
                Log::info('File is already MP4, storing without conversion. URL: ' . $originalPath);
                return $originalPath;
            }

            $originalPath = $file->storeAs('videos/original', time() . '_' . $file->getClientOriginalName(), 'public');
            Log::info('Archivo original almacenado en: ' . $originalPath);

            // Convertir el video a MP4
            $ffmpeg = FFMpeg::create([
                'ffmpeg.binaries' => '/usr/bin/ffmpeg',
                'ffprobe.binaries' => '/usr/bin/ffprobe',
                'timeout' => 3600, // tiempo máximo de procesamiento en segundos
                'ffmpeg.threads' => 12, // número de threads a usar
            ]);

            $video = $ffmpeg->open(Storage::disk('public')->path($originalPath));
            $format = new X264('libmp3lame', 'libx264');
            $convertedFilename = time() . '.mp4';
            $convertedPath = 'videos/converted/' . $convertedFilename;

            $video->save($format, Storage::disk('public')->path($convertedPath));
            Log::info('Archivo convertido almacenado en: ' . $convertedPath);

            // Eliminar el archivo original si no es MP4
            if ($originalExtension !== 'mp4') {
                Storage::disk('public')->delete($originalPath);
                Log::info('Original non-MP4 file deleted: ' . $originalPath);
            }

            return $convertedPath;
        } catch (\Exception $e) {
            // Log the error message
            Log::error('FFmpeg conversion error: ' . $e->getMessage());
            return null;
        }
    }



    public function uploadAndConvertVideo(Request $request)
    {
        $file = $request->file('video');
        $convertedUrl = $this->convertVideo($file);

        if ($convertedUrl) {
            return response()->json(['success' => true, 'url' => $convertedUrl], 200);
        } else {
            return response()->json(['success' => false, 'message' => 'Video conversion failed'], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Video $video)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Video $video)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Video $video)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Video $video)
    {
        //
    }

    public function videoConverter()
    {
    }
}
