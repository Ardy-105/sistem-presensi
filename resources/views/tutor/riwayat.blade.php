@extends('layout.presensi')

@section('title', 'Riwayat Absensi')

@section('content')
@php
    $user = auth()->user();
    $displayName = (string) (($user->nama_lengkap ?? $user->name) ?? 'Tutor');
    $initial = strtoupper(substr($displayName, 0, 1));
@endphp

<style>
    .listTop {
        background: #f3f4f6;
        padding: 12px 14px;
        border-bottom: 1px solid rgba(226,232,240,0.9);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }
    .listTopLeft { display: flex; align-items: center; gap: 10px; min-width: 0; }
    .listAvatar {
        width: 40px; height: 40px; border-radius: 50%;
        background: #111827; color: #fff; display: grid; place-items: center; font-weight: 900;
    }
    .listTitle { font-size: 13px; font-weight: 1000; color: #0f172a; }
    .listSub { font-size: 11px; color: #64748b; font-weight: 800; margin-top: 2px; }
    .backBadge {
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 1000;
        border: 1px solid rgba(226,232,240,0.95);
        background: #fff;
        color: #0f172a;
    }
    .listPad { padding: 14px 14px 110px; }
    .histCard {
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(226,232,240,0.95);
        padding: 12px;
        margin-bottom: 10px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .histDate { font-size: 12px; font-weight: 1000; color: #0f172a; }
    .histSiswa { font-size: 11px; color: #64748b; font-weight: 800; margin-top: 4px; }
    .histRow { font-size: 11px; color: #475569; font-weight: 800; margin-top: 6px; display: flex; flex-wrap: wrap; gap: 8px; align-items: center; }
    .histPill {
        font-size: 10px;
        font-weight: 1000;
        padding: 4px 10px;
        border-radius: 999px;
        border: 1px solid rgba(226,232,240,0.95);
    }
    .histPill.ok { background: rgba(22,163,74,0.12); color: #15803d; }
    .histPill.pending { background: rgba(245,158,11,0.12); color: #b45309; }
    .histPill.other { background: rgba(100,116,139,0.1); color: #475569; }
    .histPill.alpha { background: rgba(239,68,68,0.12); color: #b91c1c; border-color: rgba(239,68,68,0.2); }
    .pager { margin-top: 16px; display: flex; justify-content: center; }
    .pager ul.pagination {
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        gap: 10px;
        align-items: center;
        flex-wrap: wrap;
        justify-content: center;
    }
    .pager ul.pagination li.disabled span { opacity: 0.45; }
    .pager ul.pagination a,
    .pager ul.pagination span {
        display: inline-block;
        padding: 10px 14px;
        border-radius: 12px;
        font-weight: 900;
        font-size: 12px;
        text-decoration: none;
        border: 1px solid rgba(226,232,240,0.95);
        background: #fff;
        color: #2563eb;
    }
    .emptyBox {
        text-align: center;
        padding: 24px 14px;
        color: #64748b;
        font-weight: 800;
        font-size: 13px;
    }
</style>

<div class="listTop">
    <div class="listTopLeft">
        <div class="listAvatar">{{ $initial }}</div>
        <div>
            <div class="listTitle">Riwayat absensi</div>
            <div class="listSub">Data presensi Anda</div>
        </div>
    </div>
    <a href="{{ route('tutor.dashboard') }}" class="backBadge">
        <ion-icon name="arrow-back-outline"></ion-icon>
        Dashboard
    </a>
</div>

<div class="listPad">
    @forelse($items as $p)
        @php
            $hari = \Carbon\Carbon::parse($p->tgl_presensi)->locale('id')->translatedFormat('l, d F Y');
            $siswaLabel = $p->siswa->nama_siswa ?? ('Siswa #' . $p->siswa_id);
            $jm = $p->jam_mulai ? substr((string) $p->jam_mulai, 0, 5) : '—';
            $js = $p->jam_selesai ? substr((string) $p->jam_selesai, 0, 5) : '—';
            $st = strtoupper((string) ($p->status ?? 'pending'));
            if ($st === '' || strtolower($st) === 'pending') {
                $st = 'PENDING';
            }
            $pill = $st === 'HADIR' ? 'ok' : ($st === 'ALPHA' ? 'alpha' : (($st === 'PENDING') ? 'pending' : 'other'));
            $fotoInfo = [];
            if ($p->foto_mulai) {
                $fotoInfo[] = 'Foto masuk';
            }
            if ($p->foto_selesai) {
                $fotoInfo[] = 'Foto pulang';
            }
        @endphp
        <div class="histCard">
            <div class="histDate">{{ $hari }}</div>
            <div class="histSiswa">{{ $siswaLabel }}</div>
            <div class="histRow">
                <span>Jadwal: {{ $jm }} – {{ $js }} WIB</span>
                <span class="histPill {{ $pill }}">{{ $st }}</span>
            </div>
            @if(count($fotoInfo))
                <div class="histRow" style="margin-top:4px;color:#64748b;">
                    {{ implode(' • ', $fotoInfo) }}
                </div>
            @endif
        </div>
    @empty
        <div class="emptyBox">Belum ada riwayat absensi.</div>
    @endforelse

    @if($items->hasPages())
        <div class="pager">{{ $items->links() }}</div>
    @endif
</div>
@endsection
