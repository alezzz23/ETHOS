@extends('layouts.portal')

@section('title', '¡Gracias!')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 text-center">
        <div class="card py-5">
            <div class="card-body">
                <div class="mb-4" style="font-size:4rem">🎉</div>
                <h3 class="fw-bold mb-3">¡Gracias por tu feedback!</h3>
                <p class="text-muted">
                    Tu opinión es fundamental para que sigamos mejorando nuestros servicios.<br>
                    El equipo de <strong>ETHOS</strong> ha recibido tus respuestas.
                </p>
                <hr>
                <p class="text-muted small mb-0">
                    Proyecto: <strong>{{ $survey->project->title }}</strong>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
