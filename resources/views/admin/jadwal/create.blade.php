@extends('layout.admin')

@section('title', 'Tambah Jadwal — Admin')

<style>
    .formPage {
        padding: 0 16px 120px;
    }

    .formPageHeader {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px 16px 4px;
    }

    .backBtn {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: rgba(37,99,235,0.1);
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #2563eb;
        flex-shrink: 0;
    }

    .backBtn ion-icon { font-size: 20px; }

    .formPageTitle {
        font-size: 18px;
        font-weight: 700;
        color: #0f172a;
    }

    .formGroup {
        margin-bottom: 16px;
    }

    .formLabel {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #64748b;
        letter-spacing: 0.5px;
        margin-bottom: 7px;
        text-transform: uppercase;
    }

    .formControl {
        width: 100%;
        padding: 13px 14px;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        background: #fff;
        font-size: 14px;
        color: #0f172a;
        box-sizing: border-box;
        appearance: none;
        -webkit-appearance: none;
        font-family: inherit;
    }

    .formControl:focus {
        outline: none;
        border-color: #2563eb;
        box-shadow: 0 0 0 3px rgba(37,99,235,0.12);
    }

    .formControl.select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='%2394a3b8'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 14px center;
        background-size: 16px;
        padding-right: 40px;
    }

    .timeRow {
        display: flex;
        gap: 10px;
    }

    .timeRow .formGroup { flex: 1; }

    .errorMsg {
        font-size: 11px;
        color: #ef4444;
        margin-top: 5px;
        display: block;
    }

    .submitBtn {
        width: 100%;
        padding: 15px;
        border-radius: 14px;
        border: none;
        background: #1a3a5c;
        color: #fff;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        margin-top: 8px;
        font-family: inherit;
        box-shadow: 0 4px 14px rgba(26,58,92,0.3);
    }

    .submitBtn:active { opacity: 0.9; }

    .errorList {
        margin: 0 0 16px;
        padding: 12px 14px;
        background: rgba(239,68,68,0.08);
        border: 1px solid rgba(239,68,68,0.22);
        border-radius: 12px;
        font-size: 13px;
        color: #dc2626;
    }

    .errorList ul { margin: 6px 0 0; padding-left: 16px; }
    .errorList ul li { margin-bottom: 3px; }
</style>

@section('content')

    <div class="formPageHeader">
        <a href="{{ route('admin.jadwal.index') }}" class="backBtn">
            <ion-icon name="arrow-back-outline"></ion-icon>
        </a>
        <div class="formPageTitle">Tambah Jadwal</div>
    </div>

    <div class="formPage">

        @if($errors->any())
            <div class="errorList">
                <strong>Terdapat kesalahan:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.jadwal.store') }}">
            @csrf

            <div class="formGroup">
                <label class="formLabel">Tutor</label>
                <select name="tutor_id" class="formControl select" required>
                    <option value="">— Pilih Tutor —</option>
                    @foreach($tutors as $tutor)
                        <option value="{{ $tutor->id }}" {{ old('tutor_id') == $tutor->id ? 'selected' : '' }}>
                            {{ $tutor->nama_lengkap }}
                        </option>
                    @endforeach
                </select>
                @error('tutor_id')
                    <span class="errorMsg">{{ $message }}</span>
                @enderror
            </div>

            <div class="formGroup">
                <label class="formLabel">Siswa</label>
                <select name="siswa_id" class="formControl select" required>
                    <option value="">— Pilih Siswa —</option>
                    @foreach($siswas as $siswa)
                        <option value="{{ $siswa->id }}" {{ old('siswa_id') == $siswa->id ? 'selected' : '' }}>
                            {{ $siswa->nama_siswa }}
                        </option>
                    @endforeach
                </select>
                @error('siswa_id')
                    <span class="errorMsg">{{ $message }}</span>
                @enderror
            </div>

            <div class="formGroup">
                <label class="formLabel">Mata Pelajaran</label>
                <input type="text" name="mata_pelajaran" class="formControl"
                       value="{{ old('mata_pelajaran') }}" placeholder="Contoh: Matematika" required>
                @error('mata_pelajaran')
                    <span class="errorMsg">{{ $message }}</span>
                @enderror
            </div>

            <div class="formGroup">
                <label class="formLabel">Tanggal</label>
                <input type="date" name="tanggal" class="formControl"
                       value="{{ old('tanggal', date('Y-m-d')) }}" required>
                @error('tanggal')
                    <span class="errorMsg">{{ $message }}</span>
                @enderror
            </div>

            <div class="timeRow">
                <div class="formGroup">
                    <label class="formLabel">Jam Mulai</label>
                    <input type="time" name="jam_mulai" class="formControl"
                           value="{{ old('jam_mulai') }}" required>
                    @error('jam_mulai')
                        <span class="errorMsg">{{ $message }}</span>
                    @enderror
                </div>
                <div class="formGroup">
                    <label class="formLabel">Jam Selesai</label>
                    <input type="time" name="jam_selesai" class="formControl"
                           value="{{ old('jam_selesai') }}" required>
                    @error('jam_selesai')
                        <span class="errorMsg">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <button type="submit" class="submitBtn">Simpan Jadwal</button>
        </form>
    </div>

@endsection
