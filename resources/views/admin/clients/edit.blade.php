@extends('layouts.vuexy')

@section('title', 'Editar Cliente')

@section('content')
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Modificar Cliente</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('clients.update', $client->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Nombre *</label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $client->name) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Industria</label>
                        <input type="text" name="industry" class="form-control" value="{{ old('industry', $client->industry) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nombre Contacto Principal</label>
                        <input type="text" name="primary_contact_name" class="form-control" value="{{ old('primary_contact_name', $client->primary_contact_name) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email Contacto Principal</label>
                        <input type="email" name="primary_contact_email" class="form-control" value="{{ old('primary_contact_email', $client->primary_contact_email) }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Notas</label>
                        <textarea name="notes" class="form-control" rows="3">{{ old('notes', $client->notes) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar</button>
                    <a href="{{ route('clients.index') }}" class="btn btn-label-secondary">Cancelar</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
