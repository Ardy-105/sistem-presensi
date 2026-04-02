@extends('layout.presensi')

@section('title', 'Presensi Foto')

@section('content')
@php
    $user = auth()->user();
    $displayName = (string) (($user->nama_lengkap ?? $user->name) ?? 'Tutor');
    $initial = strtoupper(substr($displayName, 0, 1));
    $selectedSiswa = (int) old('siswa_id', request('siswa_id', 0));
@endphp

<style>
    .pagePad { padding: 14px 14px 110px; }

    .headRow {
        display:flex; align-items:center; justify-content:space-between; gap:12px;
        padding: 12px 14px;
        background: #f3f4f6;
        border-bottom: 1px solid rgba(226,232,240,0.9);
    }
    .headLeft { display:flex; align-items:center; gap:10px; min-width:0; }
    .headAvatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: #111827; color: #fff; display:grid; place-items:center; font-weight: 900;
    }
    .headMeta { min-width:0; }
    .headName { font-size: 13px; font-weight: 1000; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .headSub { font-size: 11px; color:#64748b; font-weight: 900; margin-top:2px; }

    .segmented {
        display:flex; gap:8px;
        background: rgba(15,23,42,0.06);
        padding: 6px;
        border-radius: 14px;
        border: 1px solid rgba(226,232,240,0.9);
    }
    .segBtn {
        border: none;
        background: transparent;
        padding: 8px 10px;
        border-radius: 12px;
        font-weight: 1000;
        font-size: 12px;
        color:#0f172a;
        cursor:pointer;
        display:flex;
        align-items:center;
        gap:6px;
        white-space:nowrap;
    }
    .segBtn.active {
        background: #ffffff;
        box-shadow: 0 6px 16px rgba(15,23,42,0.10);
        border: 1px solid rgba(226,232,240,0.9);
    }

    .select {
        width:100%;
        padding: 12px 12px;
        border-radius: 14px;
        border: 1px solid rgba(226,232,240,0.95);
        background:#fff;
        font-size: 13px;
        font-weight: 800;
        color:#0f172a;
        outline:none;
    }

    .card {
        margin-top: 12px;
        background: #ffffff;
        border-radius: 16px;
        border: 1px solid rgba(226,232,240,0.95);
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
        padding: 12px;
    }
    .cardTitle {
        display:flex; align-items:center; gap:8px;
        font-size: 12px; font-weight: 1000; color:#0f172a;
        margin-bottom: 10px;
    }
    .mapBox {
        width:100%;
        height: 110px;
        border-radius: 12px;
        background: linear-gradient(180deg,#f1f5f9 0%, #e2e8f0 100%);
        border: 1px solid rgba(226,232,240,0.95);
        display:flex;
        align-items:center;
        justify-content:center;
        color:#64748b;
        font-weight: 900;
        font-size: 12px;
        overflow:hidden;
        position: relative;
    }
    .mapHint {
        position:absolute;
        bottom: 8px;
        left: 10px;
        right: 10px;
        font-size: 10px;
        color:#64748b;
        font-weight: 900;
        opacity: 0.9;
        text-align:center;
    }

    .photoFrame {
        width: 100%;
        aspect-ratio: 1 / 1;
        border-radius: 16px;
        margin-top: 12px;
        background: #f8fafc;
        border: 2px solid rgba(226,232,240,0.95);
        display:flex;
        align-items:center;
        justify-content:center;
        overflow:hidden;
        position: relative;
    }
    .photoFrame img { width:100%; height:100%; object-fit: cover; display:none; }
    .photoPlaceholder {
        color:#94a3b8;
        font-weight: 1000;
        font-size: 12px;
        text-align:center;
        padding: 12px;
    }
    .captureBtn {
        margin-top: 14px;
        width: 100%;
        border: none;
        border-radius: 16px;
        padding: 14px 12px;
        background: #e8fbff;
        border: 1px solid rgba(2,132,199,0.12);
        color: #2563eb;
        font-weight: 1000;
        display:flex;
        align-items:center;
        justify-content:center;
        gap: 10px;
        cursor:pointer;
    }
    .hint {
        margin-top: 10px;
        font-size: 11px;
        color:#64748b;
        font-weight: 800;
        text-align:center;
    }
    .badge {
        display:inline-flex;
        align-items:center;
        gap:6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 1000;
        border: 1px solid rgba(226,232,240,0.95);
        background: #fff;
        color:#0f172a;
    }
</style>

<div class="headRow">
    <div class="headLeft">
        <div class="headAvatar">{{ $initial }}</div>
        <div class="headMeta">
            <div class="headName">{{ $displayName }}</div>
            <div class="headSub">Presensi Foto • {{ \Carbon\Carbon::parse($today)->translatedFormat('d M Y') }}</div>
        </div>
    </div>
    <a href="{{ route('tutor.dashboard') }}" class="badge" style="text-decoration:none;">
        <ion-icon name="grid-outline"></ion-icon>
        Dashboard
    </a>
</div>

<div class="pagePad">
    @if($jadwals->isEmpty())
        <div class="card" style="text-align:center;">
            <div style="font-weight:1000;">Tidak ada jadwal hari ini.</div>
            <div style="margin-top:6px;color:#64748b;font-weight:900;font-size:12px;">
                Hubungi admin untuk menambahkan jadwal.
            </div>
        </div>
    @else
        <form method="POST" action="{{ route('tutor.presensi.store') }}" enctype="multipart/form-data" id="presensiForm">
            @csrf

            <div class="segmented" role="tablist" aria-label="Mode presensi">
                <button type="button" class="segBtn active" id="btnMulai" onclick="setMode('mulai')">
                    <ion-icon name="log-in-outline"></ion-icon>
                    Masuk
                </button>
                <button type="button" class="segBtn" id="btnSelesai" onclick="setMode('selesai')">
                    <ion-icon name="log-out-outline"></ion-icon>
                    Pulang
                </button>
                <input type="hidden" name="mode" id="mode" value="mulai" />
            </div>

            <div style="margin-top:12px;">
                <select class="select" name="siswa_id" id="siswaSelect" required>
                    <option value="" disabled {{ $selectedSiswa ? '' : 'selected' }}>Nama Siswa</option>
                    @foreach($jadwals as $j)
                        @php
                            $siswa = $j->siswa;
                            $p = $presensiToday->get($j->siswa_id);
                            $badge = '';
                            if ($p?->foto_mulai && $p?->foto_selesai) $badge = ' (Selesai)';
                            elseif ($p?->foto_mulai) $badge = ' (Masuk)';
                        @endphp
                        <option value="{{ $j->siswa_id }}" {{ (int) old('siswa_id') === (int) $j->siswa_id ? 'selected' : '' }}>
                            {{ $siswa->nama_siswa ?? ('Siswa #' . $j->siswa_id) }} • {{ substr((string)$j->jam_mulai,0,5) }}-{{ substr((string)$j->jam_selesai,0,5) }}{{ $badge }}
                        </option>
                    @endforeach
                </select>
            </div>

            <input type="hidden" name="lokasi" id="lokasi" value="{{ old('lokasi') }}" />

            <div class="card">
                <div class="cardTitle">
                    <ion-icon name="location-outline"></ion-icon>
                    Lokasi Presensi
                </div>
                <div class="mapBox" id="mapBox">
                    Memuat lokasi…
                    <div class="mapHint" id="mapHint">Pastikan GPS aktif untuk akurasi.</div>
                </div>
            </div>

            <div class="card">
                <div class="cardTitle">
                    <ion-icon name="camera-outline"></ion-icon>
                    Foto Presensi
                </div>

                <div class="photoFrame">
                    <img id="previewImg" alt="Preview foto presensi" />
                    <div class="photoPlaceholder" id="placeholder">
                        Ambil foto wajah dengan jelas.<br/>
                        Pastikan pencahayaan cukup.
                    </div>
                </div>

                <input
                    type="file"
                    name="foto"
                    id="fotoInput"
                    accept="image/*"
                    capture="environment"
                    style="display:none;"
                    required
                />

                <button type="button" class="captureBtn" onclick="triggerCamera()">
                    <ion-icon name="camera" style="font-size:22px;"></ion-icon>
                    Ambil Foto
                </button>

                <button type="submit" class="captureBtn" style="background:#1f3b8a;color:#fff;border-color:rgba(31,59,138,0.25);">
                    <ion-icon name="save-outline" style="font-size:20px;"></ion-icon>
                    Simpan Presensi
                </button>

                <div class="hint">
                    Presensi hanya bisa dilakukan pada jadwal hari ini (dengan toleransi waktu).
                </div>
            </div>
        </form>
    @endif
</div>

<script>
    function setMode(mode) {
        document.getElementById('mode').value = mode;
        const mulai = document.getElementById('btnMulai');
        const selesai = document.getElementById('btnSelesai');
        if (mode === 'mulai') {
            mulai.classList.add('active');
            selesai.classList.remove('active');
        } else {
            selesai.classList.add('active');
            mulai.classList.remove('active');
        }
    }

    function triggerCamera() {
        const select = document.getElementById('siswaSelect');
        if (!select.value) {
            alert('Pilih siswa terlebih dahulu.');
            return;
        }
        document.getElementById('fotoInput').click();
    }

    document.getElementById('fotoInput')?.addEventListener('change', function (e) {
        const file = e.target.files && e.target.files[0];
        if (!file) return;
        const url = URL.createObjectURL(file);
        const img = document.getElementById('previewImg');
        const ph = document.getElementById('placeholder');
        img.src = url;
        img.style.display = 'block';
        ph.style.display = 'none';
    });

    (function initGeo() {
        const mapBox = document.getElementById('mapBox');
        const mapHint = document.getElementById('mapHint');
        const lokasi = document.getElementById('lokasi');
        if (!navigator.geolocation) {
            mapBox.textContent = 'Geolocation tidak didukung.';
            return;
        }

        navigator.geolocation.getCurrentPosition(function (pos) {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            lokasi.value = lat.toFixed(6) + ',' + lng.toFixed(6);
            mapBox.textContent = 'Lokasi terdeteksi';
            mapHint.textContent = 'Lat,Lng: ' + lokasi.value;
        }, function () {
            mapBox.textContent = 'Gagal mengambil lokasi.';
            mapHint.textContent = 'Pastikan izin lokasi diaktifkan.';
        }, { enableHighAccuracy: true, timeout: 8000 });
    })();
</script>
@endsection

