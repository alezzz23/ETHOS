@extends('layouts.portal')

@section('title', 'Encuesta de Satisfacción')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">

        <div class="card mb-4">
            <div class="card-body text-center py-4">
                <h4 class="fw-bold mb-1">Encuesta de Satisfacción</h4>
                <p class="text-muted mb-0">
                    Proyecto: <strong>{{ $survey->project->title }}</strong><br>
                    <small>{{ $survey->project->client?->name }}</small>
                </p>
            </div>
        </div>

        <form method="POST" action="{{ url('/survey/' . $survey->token) }}">
            @csrf

            {{-- NPS --}}
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">
                        ¿Con qué probabilidad nos recomendarías a un colega o conocido?
                        <span class="text-danger">*</span>
                    </label>
                    <p class="text-muted small mb-3">0 = Nada probable &nbsp;&bull;&nbsp; 10 = Muy probable</p>
                    <div class="d-flex gap-2 flex-wrap">
                        @for($i = 0; $i <= 10; $i++)
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                name="nps_score" id="nps_{{ $i }}" value="{{ $i }}"
                                {{ old('nps_score') == $i ? 'checked' : '' }} required>
                            <label class="form-check-label d-flex align-items-center justify-content-center
                                rounded fw-semibold"
                                for="nps_{{ $i }}"
                                style="width:38px;height:38px;cursor:pointer;border:2px solid #dee2e6;
                                background:{{ $i <= 6 ? '#fff5f5' : ($i <= 8 ? '#fffaf0' : '#f0fff4') }}">
                                {{ $i }}
                            </label>
                        </div>
                        @endfor
                    </div>
                    @error('nps_score') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                </div>
            </div>

            {{-- CES --}}
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">
                        ¿Cuánto esfuerzo requirió trabajar con nosotros?
                    </label>
                    <p class="text-muted small mb-3">1 = Muy bajo esfuerzo &nbsp;&bull;&nbsp; 7 = Muy alto esfuerzo</p>
                    <div class="d-flex gap-2">
                        @for($i = 1; $i <= 7; $i++)
                        <div class="form-check">
                            <input class="form-check-input" type="radio"
                                name="ces_score" id="ces_{{ $i }}" value="{{ $i }}"
                                {{ old('ces_score') == $i ? 'checked' : '' }}>
                            <label class="form-check-label d-flex align-items-center justify-content-center
                                rounded fw-semibold"
                                for="ces_{{ $i }}"
                                style="width:38px;height:38px;cursor:pointer;border:2px solid #dee2e6">
                                {{ $i }}
                            </label>
                        </div>
                        @endfor
                    </div>
                </div>
            </div>

            {{-- CSAT --}}
            <div class="card mb-4">
                <div class="card-body">
                    <label class="form-label fw-semibold">
                        En general, ¿qué tan satisfecho estás con el proyecto?
                    </label>
                    <div class="d-flex gap-3 mt-2">
                        @foreach([1=>'😟',2=>'😕',3=>'😐',4=>'😊',5=>'😍'] as $v => $emoji)
                        <div class="form-check text-center">
                            <input class="form-check-input d-none" type="radio"
                                name="csat_score" id="csat_{{ $v }}" value="{{ $v }}"
                                {{ old('csat_score') == $v ? 'checked' : '' }}>
                            <label for="csat_{{ $v }}"
                                style="font-size:2rem;cursor:pointer;opacity:{{ old('csat_score') == $v ? 1 : 0.5 }}">
                                {{ $emoji }}
                            </label>
                            <div class="small text-muted">{{ $v }}</div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Open text --}}
            <div class="card mb-4">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">¿Qué salió bien?</label>
                        <textarea name="what_went_well" class="form-control" rows="3"
                            placeholder="Cuéntanos qué aspectos destacarías...">{{ old('what_went_well') }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">¿Qué podemos mejorar?</label>
                        <textarea name="what_could_improve" class="form-control" rows="3"
                            placeholder="Tu feedback es muy valioso...">{{ old('what_could_improve') }}</textarea>
                    </div>
                    <div>
                        <label class="form-label fw-semibold">Comentarios adicionales</label>
                        <textarea name="additional_comments" class="form-control" rows="2"
                            placeholder="Cualquier otro comentario...">{{ old('additional_comments') }}</textarea>
                    </div>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="ti ti-send me-2"></i>Enviar encuesta
                </button>
            </div>
        </form>

    </div>
</div>
@endsection
