@extends('layouts.dashboard')
@section('title','Setari cont')
@section('page_title','Setari cont')

@section('content')
<div style="max-width:580px;display:flex;flex-direction:column;gap:18px">

    <div class="card card-body">
        <div class="form-sec">
            <div class="form-sec-title">Informatii profil</div>
            <div class="form-sec-desc">Actualizeaza numele si adresa de email asociate contului tau.</div>
            <form method="POST" action="{{ route('dashboard.profile.update') }}">
                @csrf @method('PATCH')
                <div class="form-grid">
                    <div class="field">
                        <label class="field-label" for="name">Nume</label>
                        <input type="text" id="name" name="name" class="field-input" value="{{ old('name', $user->name) }}" required/>
                        @error('name')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label class="field-label" for="email">Email</label>
                        <input type="email" id="email" name="email" class="field-input" value="{{ old('email', $user->email) }}" required/>
                        @error('email')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                </div>
                <button type="submit" class="btn btn-dark btn-md">Salveaza modificarile</button>
            </form>
        </div>
    </div>

    <div class="card card-body">
        <div class="form-sec">
            <div class="form-sec-title">Schimba parola</div>
            <div class="form-sec-desc">Foloseste o parola puternica de minim 8 caractere.</div>
            <form method="POST" action="{{ route('dashboard.password.update') }}">
                @csrf @method('PATCH')
                <div class="field">
                    <label class="field-label" for="current_password">Parola curenta</label>
                    <input type="password" id="current_password" name="current_password" class="field-input" required/>
                    @error('current_password')<p class="field-error">{{ $message }}</p>@enderror
                </div>
                <div class="form-grid">
                    <div class="field">
                        <label class="field-label" for="password">Parola noua</label>
                        <input type="password" id="password" name="password" class="field-input" required/>
                        <p class="field-hint">Minim 8 caractere.</p>
                        @error('password')<p class="field-error">{{ $message }}</p>@enderror
                    </div>
                    <div class="field">
                        <label class="field-label" for="password_confirmation">Confirma parola</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="field-input" required/>
                    </div>
                </div>
                <button type="submit" class="btn btn-dark btn-md">Schimba parola</button>
            </form>
        </div>
    </div>

    <div class="card card-body">
        <div class="form-sec-title" style="margin-bottom:14px">Informatii cont</div>
        <div class="info-row">
            <span class="info-lbl">Cont creat</span>
            <span class="info-val">{{ $user->created_at->format('d.m.Y, H:i') }}</span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Email verificat</span>
            <span>
                @if($user->email_verified_at)
                    <span class="badge g">Verificat</span>
                @else
                    <span class="badge a">Neverificat</span>
                @endif
            </span>
        </div>
        <div class="info-row">
            <span class="info-lbl">Total audituri</span>
            <span class="info-val">{{ $user->audits()->count() }}</span>
        </div>
    </div>

</div>
@endsection