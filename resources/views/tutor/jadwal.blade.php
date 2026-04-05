@extends('layout.presensi')

@section('title', 'Agenda')

@section('content')
@php
    $user = auth()->user();
    $displayName = (string) (($user->nama_lengkap ?? $user->name) ?? 'Tutor');
    $initial = strtoupper(substr($displayName, 0, 1));

    use Carbon\Carbon;
    $todayDate = Carbon::today();

    // generate 7 hari (calendar horizontal)
    $dates = [];
    for ($i = -3; $i <= 3; $i++) {
        $dates[] = $todayDate->copy()->addDays($i);
    }
@endphp

<style>
body {
    background: #f3f4f6;
}

/* HEADER */
.tutorTop {
        background: #f3f4f6;
        padding: 12px 14px;
        border-bottom: 1px solid rgba(226,232,240,0.9);
    }
    .tutorTopRow { display:flex; align-items:center; justify-content:space-between; gap:12px; }
    .tutorLeft { display:flex; align-items:center; gap:10px; min-width:0; }
    .tutorAvatar {
        width: 42px; height: 42px; border-radius: 50%;
        background: #111827; color: #fff;
        display:grid; place-items:center; font-weight: 900;
        overflow: hidden;
    }
    .tutorMeta { min-width:0; }
    .tutorName { font-size: 13px; font-weight: 900; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .tutorSub { font-size: 11px; color:#64748b; font-weight: 700; margin-top:2px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .tutorIconBtn {
        width: 36px; height: 36px;
        border-radius: 999px;
        border: none;
        background: rgba(15,23,42,0.06);
        display:grid; place-items:center;
        color:#0f172a;
        cursor:pointer;
    }

/* TITLE */
.agendaTitle {
    margin-top: 14px;
    font-size: 18px;
    font-weight: 900;
}

.agendaSub {
    font-size: 12px;
    color: #6b7280;
}

/* CALENDAR */
.calendar {
    display: flex;
    overflow-x: auto;
    gap: 10px;
    margin-top: 12px;
    padding-bottom: 5px;
}

.calItem {
    min-width: 60px;
    text-align: center;
    font-size: 11px;
    font-weight: 700;
    color: #6b7280;
}

.calItem .date {
    margin-top: 6px;
    padding: 8px;
    border-radius: 10px;
}

.calItem.active .date {
    background: #1e3a8a;
    color: #fff;
}

/* CARD */
.agendaCard {
    background: #fff;
    border-radius: 14px;
    padding: 12px;
    margin-top: 12px;
    border-left: 5px solid #2563eb;
    box-shadow: 0 2px 6px rgba(0,0,0,0.05);
}

.agendaCard.red {
    border-left: 5px solid #ef4444;
}

.label {
    font-size: 10px;
    font-weight: 900;
    color: #2563eb;
    letter-spacing: 1px;
}

.label.red {
    color: #ef4444;
}

.title {
    font-size: 13px;
    font-weight: 800;
    margin-top: 4px;
}

.time {
    font-size: 10px;
    float: right;
    color: #6b7280;
}

.location {
    font-size: 11px;
    color: #6b7280;
    margin-top: 4px;
}
</style>

<div class="tutorTop">
    <div class="tutorTopRow">
        <div class="tutorLeft">
            <div class="tutorAvatar" aria-label="Avatar">{{ $initial }}</div>
            <div class="tutorMeta">
                <div class="tutorName">{{ $displayName }}</div>
                <div class="tutorSub">Tutor</div>
            </div>
        </div>
        <button class="tutorIconBtn" type="button" aria-label="Tema">
            <ion-icon name="moon-outline" style="font-size:20px;"></ion-icon>
        </button>
    </div>
</div>

<div class="agendaTitle">Agenda Kegiatan</div>
<div class="agendaSub">{{ $todayDate->translatedFormat('F Y') }}</div>

<!-- CALENDAR HORIZONTAL -->
<div class="calendar">
    @foreach($dates as $d)
        <div class="calItem {{ $d->isToday() ? 'active' : '' }}">
            <div>{{ strtoupper($d->translatedFormat('D')) }}</div>
            <div class="date">{{ $d->format('d') }}</div>
        </div>
    @endforeach
</div>

<!-- LIST AGENDA -->
@forelse($items as $j)
    @php
        $tgl = Carbon::parse($j->tanggal);
        $jm = substr((string) $j->jam_mulai, 0, 5);
        $js = substr((string) $j->jam_selesai, 0, 5);
        $mapel = $j->mata_pelajaran ?? 'Pelajaran';
        $namaSiswa = $j->siswa->nama_siswa ?? 'Siswa';
        $isUjian = str_contains(strtolower($mapel), 'ujian');
    @endphp

    <div class="agendaCard {{ $isUjian ? 'red' : '' }}">

        <div>
            <span class="label {{ $isUjian ? 'red' : '' }}">
                {{ $isUjian ? 'UJIAN' : 'MENGAJAR' }}
            </span>

            <span class="time">
                {{ $jm }} - {{ $js }}
            </span>
        </div>

        <div class="title">
            {{ $mapel }} - {{ $namaSiswa }}
        </div>

        <div class="location">
            <strong style="color:#374151;">Lokasi:</strong> {{ $j->lokasiRingkasan() }}
            @if($mapLink = $j->lokasiPetaUrl())
                <br><a href="{{ $mapLink }}" target="_blank" rel="noopener" style="color:#2563eb;font-weight:800;">Buka di Google Maps</a>
            @endif
        </div>

    </div>

@empty
    <div style="text-align:center; margin-top:20px; color:#6b7280;">
        Tidak ada agenda
    </div>
@endforelse

@endsection
