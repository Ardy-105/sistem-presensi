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
        height: 220px;
        border-radius: 12px;
        background: linear-gradient(180deg,#f1f5f9 0%, #e2e8f0 100%);
        border: 1px solid rgba(226,232,240,0.95);
        overflow:hidden;
        position: relative;
    }
    .mapBox .gmapFrame {
        display: block;
        width: 100%;
        height: 100%;
        border: 0;
        border-radius: 12px;
    }
    .mapPlaceholder {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 16px;
        color:#64748b;
        font-weight: 900;
        font-size: 12px;
        z-index: 1;
        background: linear-gradient(180deg,#f1f5f9 0%, #e2e8f0 100%);
    }
    .mapPlaceholder.hidden { display: none; }
    .mapCoordHint {
        margin-top: 8px;
        font-size: 11px;
        color:#64748b;
        font-weight: 800;
        text-align: center;
        line-height: 1.35;
    }
    .mapToolbar {
        margin-top: 8px;
        display: flex;
        justify-content: flex-end;
    }
    .mapToolbar button {
        border: 1px solid rgba(226,232,240,0.95);
        background: #fff;
        border-radius: 10px;
        padding: 8px 12px;
        font-size: 11px;
        font-weight: 1000;
        color: #2563eb;
        cursor: pointer;
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
    .photoFrame img,
    .photoFrame video {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }
    .photoFrame video.active,
    .photoFrame img.visible { display: block; }
    .photoPlaceholder {
        color:#94a3b8;
        font-weight: 1000;
        font-size: 12px;
        text-align:center;
        padding: 12px;
    }
    .photoPlaceholder.hidden { display: none; }
    .camActions {
        margin-top: 10px;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .camRow {
        display: flex;
        gap: 8px;
    }
    .camRow .captureBtn { margin-top: 0; flex: 1; }
    .captureBtn.secondary {
        background: #fff;
        color: #64748b;
        border: 1px solid rgba(226,232,240,0.95);
    }
    .captureBtn.danger {
        background: #fef2f2;
        color: #b91c1c;
        border: 1px solid rgba(239,68,68,0.2);
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
                            $locLabel = ($j->lokasi_tipe ?? 'sekolah') === 'rumah_siswa' ? 'Rumah siswa' : 'Sekolah';
                        @endphp
                        <option value="{{ $j->siswa_id }}" {{ (int) old('siswa_id') === (int) $j->siswa_id ? 'selected' : '' }}>
                            {{ $siswa->nama_siswa ?? ('Siswa #' . $j->siswa_id) }} • {{ substr((string)$j->jam_mulai,0,5) }}-{{ substr((string)$j->jam_selesai,0,5) }} • {{ $locLabel }}{{ $badge }}
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
                    <div class="mapPlaceholder" id="mapPlaceholder">Memuat lokasi…</div>
                    <iframe
                        id="gmapFrame"
                        class="gmapFrame"
                        title="Peta lokasi presensi"
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"
                        allowfullscreen
                        style="display:none;"
                    ></iframe>
                </div>
                <div class="mapCoordHint" id="mapHint">Pastikan GPS aktif. Peta memakai Google Maps (bisa digeser & di-zoom).</div>
                <div class="mapToolbar">
                    <button type="button" onclick="refreshLocation()">Perbarui lokasi & peta</button>
                </div>
            </div>

            <div class="card">
                <div class="cardTitle">
                    <ion-icon name="camera-outline"></ion-icon>
                    Foto Presensi
                </div>

                <div class="photoFrame">
                    <video id="videoPreview" playsinline muted></video>
                    <img id="previewImg" alt="Preview foto presensi" />
                    <div class="photoPlaceholder" id="placeholder">
                        Kamera langsung (bukan galeri).<br/>
                        Tap <b>Buka kamera</b>, lalu <b>Absen</b>.
                    </div>
                </div>

                <input
                    type="file"
                    name="foto"
                    id="fotoInput"
                    accept="image/jpeg"
                    style="display:none;"
                />

                <div class="camActions" id="camActions">
                    <button type="button" class="captureBtn" id="btnBukaKamera" onclick="openLiveCamera()">
                        <ion-icon name="camera" style="font-size:22px;"></ion-icon>
                        Buka kamera
                    </button>
                    <div class="camRow" id="camRowStreaming" style="display:none;">
                        <button type="button" class="captureBtn" onclick="snapPhoto()">
                            <ion-icon name="radio-button-on" style="font-size:22px;"></ion-icon>
                            Absen
                        </button>
                        <button type="button" class="captureBtn secondary" onclick="cancelCamera()">
                            Batal
                        </button>
                    </div>
                    <button type="button" class="captureBtn secondary" id="btnUlangi" style="display:none;" onclick="retakePhoto()">
                        <ion-icon name="refresh-outline" style="font-size:20px;"></ion-icon>
                        Ulangi foto
                    </button>
                </div>

                <button type="submit" class="captureBtn" id="btnSubmitPresensi" style="background:#1f3b8a;color:#fff;border-color:rgba(31,59,138,0.25);">
                    <ion-icon name="save-outline" style="font-size:20px;"></ion-icon>
                    Simpan Presensi
                </button>

                <div class="hint">
                    Foto hanya dari kamera langsung. Lokasi di peta Google Maps.
                    <strong>Masuk:</strong> dibuka 30 menit sebelum jam mulai; setelah jam mulai = status <strong>Alpha</strong> (tidak hadir).
                    <strong>Pulang:</strong> hanya setelah jam selesai mengajar pada jadwal — lebih awal ditolak.
                </div>
            </div>
        </form>
    @endif
</div>

<script>
    let mediaStream = null;

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

    function setMapFromLatLng(lat, lng) {
        const lokasiEl = document.getElementById('lokasi');
        const frame = document.getElementById('gmapFrame');
        const ph = document.getElementById('mapPlaceholder');
        const hint = document.getElementById('mapHint');
        if (!lokasiEl || !frame || !ph || !hint) return;

        lokasiEl.value = lat.toFixed(6) + ',' + lng.toFixed(6);
        const q = encodeURIComponent(lat + ',' + lng);
        frame.src = 'https://www.google.com/maps?q=' + q + '&z=17&hl=id&output=embed';
        frame.style.display = 'block';
        ph.classList.add('hidden');
        hint.textContent = lat.toFixed(5) + ', ' + lng.toFixed(5) + ' — peta Google Maps';
    }

    function refreshLocation() {
        const ph = document.getElementById('mapPlaceholder');
        const hint = document.getElementById('mapHint');
        const frame = document.getElementById('gmapFrame');
        if (!ph || !frame) return;

        if (!navigator.geolocation) {
            ph.classList.remove('hidden');
            ph.textContent = 'Geolocation tidak didukung di peramban ini.';
            return;
        }

        ph.classList.remove('hidden');
        ph.textContent = 'Mencari lokasi…';
        if (hint) hint.textContent = 'Pastikan izin lokasi diaktifkan.';
        frame.style.display = 'none';

        navigator.geolocation.getCurrentPosition(
            function (pos) {
                setMapFromLatLng(pos.coords.latitude, pos.coords.longitude);
            },
            function () {
                ph.classList.remove('hidden');
                ph.textContent = 'Gagal mengambil lokasi.';
                if (hint) hint.textContent = 'Aktifkan GPS & izin lokasi, lalu tap Perbarui.';
            },
            { enableHighAccuracy: true, timeout: 12000, maximumAge: 0 }
        );
    }

    function stopCameraStream() {
        if (mediaStream) {
            mediaStream.getTracks().forEach(function (t) { t.stop(); });
            mediaStream = null;
        }
        const video = document.getElementById('videoPreview');
        if (video) video.srcObject = null;
    }

    function openLiveCamera() {
        const select = document.getElementById('siswaSelect');
        if (!select || !select.value) {
            alert('Pilih siswa terlebih dahulu.');
            return;
        }

        if (!navigator.mediaDevices || typeof navigator.mediaDevices.getUserMedia !== 'function') {
            alert('Peramban tidak mendukung kamera langsung. Gunakan Chrome atau Safari terbaru; untuk ponsel disarankan HTTPS (atau localhost).');
            return;
        }

        const video = document.getElementById('videoPreview');
        const tryCamera = function () {
            return navigator.mediaDevices.getUserMedia({
                video: {
                    facingMode: { ideal: 'user' },
                    width: { ideal: 1280 },
                    height: { ideal: 720 },
                },
                audio: false,
            }).catch(function () {
                return navigator.mediaDevices.getUserMedia({ video: true, audio: false });
            });
        };

        tryCamera().then(function (stream) {
            mediaStream = stream;
            video.srcObject = stream;
            video.classList.add('active');
            document.getElementById('placeholder').classList.add('hidden');
            const img = document.getElementById('previewImg');
            img.classList.remove('visible');
            img.removeAttribute('src');

            document.getElementById('btnBukaKamera').style.display = 'none';
            document.getElementById('camRowStreaming').style.display = 'flex';
            document.getElementById('btnUlangi').style.display = 'none';

            return video.play();
        }).catch(function (err) {
            alert('Tidak bisa membuka kamera: ' + (err && err.message ? err.message : 'izin ditolak atau perangkat tidak tersedia.'));
        });
    }

    function cancelCamera() {
        stopCameraStream();
        const video = document.getElementById('videoPreview');
        video.classList.remove('active');
        document.getElementById('camRowStreaming').style.display = 'none';
        document.getElementById('btnBukaKamera').style.display = 'flex';

        const input = document.getElementById('fotoInput');
        const hasFile = input && input.files && input.files.length > 0;
        if (!hasFile) {
            document.getElementById('placeholder').classList.remove('hidden');
        }
    }

    function snapPhoto() {
        const video = document.getElementById('videoPreview');
        if (!video || !video.videoWidth) {
            alert('Kamera belum siap, tunggu sebentar lalu coba lagi.');
            return;
        }

        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);

        canvas.toBlob(function (blob) {
            if (!blob) {
                alert('Gagal membuat gambar.');
                return;
            }

            try {
                var file = new File([blob], 'presensi-' + Date.now() + '.jpg', { type: 'image/jpeg' });
                var dt = new DataTransfer();
                dt.items.add(file);
                document.getElementById('fotoInput').files = dt.files;
            } catch (e) {
                alert('Peramban ini tidak mendukung penyimpanan foto dari kamera. Coba Chrome/Safari terbaru.');
                return;
            }

            var url = URL.createObjectURL(blob);
            var img = document.getElementById('previewImg');
            img.src = url;
            img.classList.add('visible');

            stopCameraStream();
            video.classList.remove('active');
            document.getElementById('camRowStreaming').style.display = 'none';
            document.getElementById('btnBukaKamera').style.display = 'none';
            document.getElementById('btnUlangi').style.display = 'flex';
            document.getElementById('placeholder').classList.add('hidden');
        }, 'image/jpeg', 0.88);
    }

    function retakePhoto() {
        document.getElementById('fotoInput').value = '';
        var img = document.getElementById('previewImg');
        img.removeAttribute('src');
        img.classList.remove('visible');
        document.getElementById('btnUlangi').style.display = 'none';
        document.getElementById('btnBukaKamera').style.display = 'flex';
        document.getElementById('placeholder').classList.remove('hidden');
    }

    var presensiForm = document.getElementById('presensiForm');
    if (presensiForm) {
        presensiForm.addEventListener('submit', function (e) {
            var input = document.getElementById('fotoInput');
            if (!input || !input.files || !input.files.length) {
                e.preventDefault();
                alert('Ambil foto dengan kamera (Buka kamera → Jepret) terlebih dahulu.');
            }
        });
    }

    if (document.getElementById('mapBox')) {
        refreshLocation();
    }
</script>
@endsection

