@extends('layout.presensi')

@section('title', 'Dashboard Tutor')

@section('content')
@php
    $user = auth()->user();
    $displayName = (string) (($user->nama_lengkap ?? $user->name) ?? 'Tutor');
    $roleLabel = 'Tutor';
    $subTitle = $user?->role ? ('Tutor ' . ucfirst(str_replace('_',' ', (string) $user->role))) : 'Tutor';
    $initial = strtoupper(substr($displayName, 0, 1));
@endphp

<style>
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
    .clockCard {
        margin: 12px 14px 0;
        border-radius: 18px;
        background: #1f3b8a;
        padding: 14px 16px;
        color: #fff;
        text-align:center;
        box-shadow: 0 10px 24px rgba(31,59,138,0.22);
    }
    .clockLabel {
        font-size: 11px;
        letter-spacing: 1px;
        font-weight: 900;
        opacity: 0.9;
    }
    .clockTime {
        font-size: 34px;
        font-weight: 1000;
        margin-top: 4px;
        line-height: 1;
        font-variant-numeric: tabular-nums;
    }
    .clockTz {
        font-size: 11px;
        opacity: 0.9;
        margin-top: 4px;
        font-weight: 800;
    }

    .sectionCaps { padding: 14px 14px 8px; }
    .sectionCapTitle { font-size: 11px; font-weight: 1000; letter-spacing: 1.2px; color:#0f172a; }

    .quickGrid {
        display:grid;
        grid-template-columns: repeat(2, minmax(0,1fr));
        gap: 12px;
        padding: 0 14px;
    }
    .quickCard {
        border-radius: 16px;
        padding: 14px 12px;
        background: #e8fbff;
        display:flex;
        align-items:center;
        justify-content:center;
        flex-direction:column;
        gap: 10px;
        text-decoration:none;
        color:#0f172a;
        border: 1px solid rgba(2,132,199,0.10);
        min-height: 104px;
    }
    .quickIcon {
        width: 54px; height: 54px; border-radius: 16px;
        display:grid; place-items:center;
        background: #e0f2fe;
        color: #2563eb;
    }
    .quickIcon.warn { background: #fef3c7; color: #d97706; }
    .quickLabel { font-size: 12px; font-weight: 900; }

    .rowHeader {
        padding: 14px 14px 8px;
        display:flex; align-items:center; justify-content:space-between; gap:12px;
    }
    .rowHeaderTitle { font-size: 11px; font-weight: 1000; letter-spacing: 1.2px; color:#0f172a; }
    .rowHeaderLink { font-size: 11px; color:#64748b; font-weight: 900; text-decoration:none; }

    .recentWrap { padding: 0 14px; display:flex; flex-direction:column; gap:10px; }
    .recentItem {
        background: #e8fbff;
        border-radius: 16px;
        padding: 12px;
        display:flex;
        align-items:center;
        justify-content:space-between;
        gap: 10px;
        border: 1px solid rgba(2,132,199,0.10);
    }
    .recentLeft { display:flex; align-items:center; gap:10px; min-width:0; }
    .recentCheck {
        width: 22px; height: 22px; border-radius: 999px;
        display:grid; place-items:center;
        background: rgba(22,163,74,0.16);
        color: #16a34a;
        flex-shrink:0;
    }
    .recentMeta { min-width:0; }
    .recentDay { font-size: 12px; font-weight: 1000; color:#0f172a; }
    .recentTime { font-size: 10px; color:#64748b; font-weight: 900; margin-top: 2px; }
    .pillSmall {
        font-size: 10px;
        font-weight: 1000;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(22,163,74,0.14);
        color:#15803d;
        border: 1px solid rgba(22,163,74,0.20);
        flex-shrink:0;
    }

    .agendaWrap { padding: 0 14px 110px; display:flex; flex-direction:column; gap:10px; }
    .agendaItem {
        background: #ffffff;
        border-radius: 16px;
        padding: 12px;
        display:flex;
        align-items:flex-start;
        gap: 10px;
        border: 1px solid rgba(226,232,240,0.9);
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .agendaBar {
        width: 4px;
        border-radius: 999px;
        background: #f59e0b;
        margin-top: 2px;
        flex-shrink: 0;
        height: 36px;
    }
    .agendaTitle { font-size: 12px; font-weight: 1000; color:#0f172a; }
    .agendaSub { font-size: 10px; color:#64748b; font-weight: 900; margin-top: 3px; }
</style>

<div class="tutorTop">
    <div class="tutorTopRow">
        <div class="tutorLeft">
            <div class="tutorAvatar" aria-label="Avatar">{{ $initial }}</div>
            <div class="tutorMeta">
                <div class="tutorName">{{ $displayName }}</div>
                <div class="tutorSub">{{ $subTitle }}</div>
            </div>
        </div>
        <button class="tutorIconBtn" type="button" aria-label="Tema">
            <ion-icon name="moon-outline" style="font-size:20px;"></ion-icon>
        </button>
    </div>
</div>

<div class="clockCard">
    <div class="clockLabel">WAKTU SAAT INI</div>
    <div class="clockTime" id="clockTime">--:--:--</div>
    <div class="clockTz">Waktu Indonesia Barat (WIB)</div>
</div>

<div class="sectionCaps">
    <div class="sectionCapTitle">AKSI CEPAT</div>
</div>

<div class="quickGrid">
    <a href="{{ route('tutor.presensi') }}" class="quickCard" aria-label="Absen">
        <div class="quickIcon">
            <ion-icon name="camera-outline" style="font-size:26px;"></ion-icon>
        </div>
        <div class="quickLabel">Absen</div>
    </a>
    <a href="#" class="quickCard" aria-label="Izin">
        <div class="quickIcon warn">
            <ion-icon name="alert-circle-outline" style="font-size:26px;"></ion-icon>
        </div>
        <div class="quickLabel">Izin</div>
    </a>
</div>

<div class="rowHeader">
    <div class="rowHeaderTitle">RIWAYAT TERBARU</div>
    <a href="#" class="rowHeaderLink">Lihat Semua</a>
</div>

<div class="recentWrap">
    <div class="recentItem">
        <div class="recentLeft">
            <div class="recentCheck">
                <ion-icon name="checkmark" style="font-size:14px;"></ion-icon>
            </div>
            <div class="recentMeta">
                <div class="recentDay">Senin, 09 Februari</div>
                <div class="recentTime">08.00</div>
            </div>
        </div>
        <div class="pillSmall">HADIR</div>
    </div>
    <div class="recentItem">
        <div class="recentLeft">
            <div class="recentCheck">
                <ion-icon name="checkmark" style="font-size:14px;"></ion-icon>
            </div>
            <div class="recentMeta">
                <div class="recentDay">Senin, 09 Februari</div>
                <div class="recentTime">08.00</div>
            </div>
        </div>
        <div class="pillSmall">HADIR</div>
    </div>
</div>

<div class="rowHeader">
    <div class="rowHeaderTitle">AGENDA TERDEKAT</div>
</div>

<div class="agendaWrap">
    <div class="agendaItem">
        <div class="agendaBar"></div>
        <div>
            <div class="agendaTitle">Ujian Tengah Semester</div>
            <div class="agendaSub">20 Januari 2026 • 08:00 WIB</div>
        </div>
    </div>
    <div class="agendaItem">
        <div class="agendaBar"></div>
        <div>
            <div class="agendaTitle">Ujian Tengah Semester</div>
            <div class="agendaSub">20 Januari 2026 • 08:00 WIB</div>
        </div>
    </div>
</div>

<script>
    (function () {
        const el = document.getElementById('clockTime');
        if (!el) return;

        const fmt = new Intl.DateTimeFormat('id-ID', {
            timeZone: 'Asia/Jakarta',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: false
        });

        function tick() {
            el.textContent = fmt.format(new Date());
        }

        tick();
        setInterval(tick, 1000);
    })();
</script>
@endsection

