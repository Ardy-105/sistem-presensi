@extends('layout.presensi')

@section('content')
    <div class="pageHeaderRow">
        <h2>Edit Siswa</h2>
        <a class="btnOutline" href="{{ route('admin.siswa.index') }}">
            Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="errorList">
            <div style="font-weight:1000;margin-bottom:6px;">Periksa input berikut:</div>
            <ul style="padding-left:18px;margin:0;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="formCard">
        <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}">
            @csrf
            @method('PUT')

            <div class="formRow">
                <div class="fieldLabel">NIS</div>
                <input class="input" type="text" name="nis" value="{{ old('nis', $siswa->nis) }}" required />
            </div>

            <div class="formRow">
                <div class="fieldLabel">Nama Siswa</div>
                <input class="input" type="text" name="nama_siswa" value="{{ old('nama_siswa', $siswa->nama_siswa) }}" required />
            </div>

            <div class="formRow">
                <div class="fieldLabel">Alamat</div>
                <input class="input" type="text" name="alamat" value="{{ old('alamat', $siswa->alamat) }}" />
            </div>

            <div class="formRow">
                <div class="fieldLabel">No HP</div>
                <input class="input" type="text" name="no_hp" value="{{ old('no_hp', $siswa->no_hp) }}" required />
            </div>

            <div class="formRow">
                <div class="fieldLabel">Nama Wali</div>
                <input class="input" type="text" name="nama_wali" value="{{ old('nama_wali', $siswa->nama_wali) }}" required />
            </div>

            <div class="formRow">
                <div class="fieldLabel">Kelas</div>
                <select class="input" name="kelas_id" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach ($kelas as $k)
                        <option value="{{ $k->id }}" {{ (int) old('kelas_id', $siswa->kelas_id) === (int) $k->id ? 'selected' : '' }}>
                            {{ $k->nama_kelas }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btnPrimary" style="width:100%;justify-content:center;">
                <ion-icon name="save-outline"></ion-icon>
                Update
            </button>
        </form>
    </div>
@endsection

